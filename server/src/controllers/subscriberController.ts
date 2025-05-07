import { Request, Response } from 'express';
import { prisma } from '../config/database';
import { logError, logInfo } from '../services/loggingService';

interface AuthRequest extends Request {
    user?: {
        id: string;
        email: string;
        role: string;
    };
}

// Create a new subscriber
export const createSubscriber = async (req: AuthRequest, res: Response) => {
    try {
        const { subscriberListId } = req.body;

        // Check if user owns the subscriber list
        const subscriberList = await prisma.subscriberList.findUnique({
            where: { id: subscriberListId }
        });

        if (!subscriberList) {
            return res.status(404).json({ message: 'Subscriber list not found' });
        }

        if (subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        const subscriber = await prisma.subscriber.create({
            data: req.body
        });

        logInfo(`Subscriber created: ${subscriber.id}`);
        res.status(201).json(subscriber);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error creating subscriber' });
    }
};

// Get all subscribers for a list
export const getSubscribers = async (req: AuthRequest, res: Response) => {
    try {
        const { subscriberListId } = req.query;

        // Check if user owns the subscriber list
        const subscriberList = await prisma.subscriberList.findUnique({
            where: { id: subscriberListId as string }
        });

        if (!subscriberList) {
            return res.status(404).json({ message: 'Subscriber list not found' });
        }

        if (subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        const subscribers = await prisma.subscriber.findMany({
            where: { subscriberListId: subscriberListId as string },
            include: {
                _count: {
                    select: {
                        emailsSent: true,
                        emailsOpened: true,
                        emailsClicked: true
                    }
                }
            }
        });

        res.json(subscribers);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error fetching subscribers' });
    }
};

// Get a specific subscriber
export const getSubscriber = async (req: AuthRequest, res: Response) => {
    try {
        const subscriber = await prisma.subscriber.findUnique({
            where: { id: req.params.id },
            include: {
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

        if (!subscriber) {
            return res.status(404).json({ message: 'Subscriber not found' });
        }

        // Check if user owns the subscriber list
        if (subscriber.subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        res.json(subscriber);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error fetching subscriber' });
    }
};

// Update a subscriber
export const updateSubscriber = async (req: AuthRequest, res: Response) => {
    try {
        const subscriber = await prisma.subscriber.findUnique({
            where: { id: req.params.id },
            include: { subscriberList: true }
        });

        if (!subscriber) {
            return res.status(404).json({ message: 'Subscriber not found' });
        }

        // Check if user owns the subscriber list
        if (subscriber.subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        const updatedSubscriber = await prisma.subscriber.update({
            where: { id: req.params.id },
            data: req.body
        });

        logInfo(`Subscriber updated: ${updatedSubscriber.id}`);
        res.json(updatedSubscriber);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error updating subscriber' });
    }
};

// Delete a subscriber
export const deleteSubscriber = async (req: AuthRequest, res: Response) => {
    try {
        const subscriber = await prisma.subscriber.findUnique({
            where: { id: req.params.id },
            include: { subscriberList: true }
        });

        if (!subscriber) {
            return res.status(404).json({ message: 'Subscriber not found' });
        }

        // Check if user owns the subscriber list
        if (subscriber.subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        await prisma.subscriber.delete({
            where: { id: req.params.id }
        });

        logInfo(`Subscriber deleted: ${req.params.id}`);
        res.status(204).send();
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error deleting subscriber' });
    }
};

// Unsubscribe a subscriber
export const unsubscribe = async (req: Request, res: Response) => {
    try {
        const { token } = req.query;

        if (!token) {
            return res.status(400).json({ message: 'No unsubscribe token provided' });
        }

        // Find subscriber by unsubscribe token
        const subscriber = await prisma.subscriber.findFirst({
            where: { id: token as string }
        });

        if (!subscriber) {
            return res.status(404).json({ message: 'Subscriber not found' });
        }

        // Update subscriber status
        const updatedSubscriber = await prisma.subscriber.update({
            where: { id: subscriber.id },
            data: { unsubscribed: true }
        });

        logInfo(`Subscriber unsubscribed: ${updatedSubscriber.id}`);
        res.json({ message: 'Successfully unsubscribed' });
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error processing unsubscribe request' });
    }
}; 