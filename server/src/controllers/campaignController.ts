import { Request, Response } from 'express';
import { prisma } from '../config/database';
import { logError, logInfo } from '../services/loggingService';
import { emailQueue } from '../services/queueService';

interface AuthRequest extends Request {
    user?: {
        id: string;
        email: string;
        role: string;
    };
}

// Create a new campaign
export const createCampaign = async (req: AuthRequest, res: Response) => {
    try {
        const campaign = await prisma.campaign.create({
            data: {
                ...req.body,
                userId: req.user?.id,
                status: 'DRAFT'
            }
        });

        logInfo(`Campaign created: ${campaign.id}`);
        res.status(201).json(campaign);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error creating campaign' });
    }
};

// Get all campaigns for a user
export const getCampaigns = async (req: AuthRequest, res: Response) => {
    try {
        const campaigns = await prisma.campaign.findMany({
            where: { userId: req.user?.id },
            include: {
                template: true,
                subscriberList: true,
                _count: {
                    select: {
                        emailsSent: true,
                        emailsOpened: true,
                        emailsClicked: true
                    }
                }
            }
        });

        res.json(campaigns);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error fetching campaigns' });
    }
};

// Get a specific campaign
export const getCampaign = async (req: AuthRequest, res: Response) => {
    try {
        const campaign = await prisma.campaign.findUnique({
            where: { id: req.params.id },
            include: {
                template: true,
                subscriberList: true,
                _count: {
                    select: {
                        emailsSent: true,
                        emailsOpened: true,
                        emailsClicked: true
                    }
                }
            }
        });

        if (!campaign) {
            return res.status(404).json({ message: 'Campaign not found' });
        }

        if (campaign.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        res.json(campaign);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error fetching campaign' });
    }
};

// Update a campaign
export const updateCampaign = async (req: AuthRequest, res: Response) => {
    try {
        const campaign = await prisma.campaign.findUnique({
            where: { id: req.params.id }
        });

        if (!campaign) {
            return res.status(404).json({ message: 'Campaign not found' });
        }

        if (campaign.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        if (campaign.status !== 'DRAFT') {
            return res.status(400).json({ message: 'Can only update draft campaigns' });
        }

        const updatedCampaign = await prisma.campaign.update({
            where: { id: req.params.id },
            data: req.body
        });

        logInfo(`Campaign updated: ${updatedCampaign.id}`);
        res.json(updatedCampaign);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error updating campaign' });
    }
};

// Delete a campaign
export const deleteCampaign = async (req: AuthRequest, res: Response) => {
    try {
        const campaign = await prisma.campaign.findUnique({
            where: { id: req.params.id }
        });

        if (!campaign) {
            return res.status(404).json({ message: 'Campaign not found' });
        }

        if (campaign.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        if (campaign.status === 'RUNNING') {
            return res.status(400).json({ message: 'Cannot delete running campaign' });
        }

        await prisma.campaign.delete({
            where: { id: req.params.id }
        });

        logInfo(`Campaign deleted: ${req.params.id}`);
        res.status(204).send();
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error deleting campaign' });
    }
};

// Start a campaign
export const startCampaign = async (req: AuthRequest, res: Response) => {
    try {
        const campaign = await prisma.campaign.findUnique({
            where: { id: req.params.id },
            include: {
                template: true,
                subscriberList: {
                    include: {
                        subscribers: true
                    }
                }
            }
        });

        if (!campaign) {
            return res.status(404).json({ message: 'Campaign not found' });
        }

        if (campaign.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        if (campaign.status !== 'DRAFT') {
            return res.status(400).json({ message: 'Campaign is not in draft status' });
        }

        // Update campaign status
        const updatedCampaign = await prisma.campaign.update({
            where: { id: req.params.id },
            data: { status: 'RUNNING', startedAt: new Date() }
        });

        // Add emails to queue
        for (const subscriber of campaign.subscriberList.subscribers) {
            await emailQueue.add({
                campaignId: campaign.id,
                subscriberId: subscriber.id,
                email: subscriber.email,
                subject: campaign.subject,
                content: campaign.template.content
            });
        }

        logInfo(`Campaign started: ${campaign.id}`);
        res.json(updatedCampaign);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error starting campaign' });
    }
};

// Pause a campaign
export const pauseCampaign = async (req: AuthRequest, res: Response) => {
    try {
        const campaign = await prisma.campaign.findUnique({
            where: { id: req.params.id }
        });

        if (!campaign) {
            return res.status(404).json({ message: 'Campaign not found' });
        }

        if (campaign.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        if (campaign.status !== 'RUNNING') {
            return res.status(400).json({ message: 'Campaign is not running' });
        }

        const updatedCampaign = await prisma.campaign.update({
            where: { id: req.params.id },
            data: { status: 'PAUSED' }
        });

        // Pause the queue processing for this campaign
        await emailQueue.pause();

        logInfo(`Campaign paused: ${campaign.id}`);
        res.json(updatedCampaign);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error pausing campaign' });
    }
};

// Resume a campaign
export const resumeCampaign = async (req: AuthRequest, res: Response) => {
    try {
        const campaign = await prisma.campaign.findUnique({
            where: { id: req.params.id }
        });

        if (!campaign) {
            return res.status(404).json({ message: 'Campaign not found' });
        }

        if (campaign.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        if (campaign.status !== 'PAUSED') {
            return res.status(400).json({ message: 'Campaign is not paused' });
        }

        const updatedCampaign = await prisma.campaign.update({
            where: { id: req.params.id },
            data: { status: 'RUNNING' }
        });

        // Resume the queue processing
        await emailQueue.resume();

        logInfo(`Campaign resumed: ${campaign.id}`);
        res.json(updatedCampaign);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error resuming campaign' });
    }
};

// Stop a campaign
export const stopCampaign = async (req: AuthRequest, res: Response) => {
    try {
        const campaign = await prisma.campaign.findUnique({
            where: { id: req.params.id }
        });

        if (!campaign) {
            return res.status(404).json({ message: 'Campaign not found' });
        }

        if (campaign.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        if (!['RUNNING', 'PAUSED'].includes(campaign.status)) {
            return res.status(400).json({ message: 'Campaign is not running or paused' });
        }

        const updatedCampaign = await prisma.campaign.update({
            where: { id: req.params.id },
            data: { status: 'COMPLETED', completedAt: new Date() }
        });

        // Remove remaining jobs from the queue for this campaign
        await emailQueue.clean(0, 'delayed');
        await emailQueue.clean(0, 'wait');

        logInfo(`Campaign stopped: ${campaign.id}`);
        res.json(updatedCampaign);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error stopping campaign' });
    }
}; 