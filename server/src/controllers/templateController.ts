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

// Create a new template
export const createTemplate = async (req: AuthRequest, res: Response) => {
    try {
        const template = await prisma.template.create({
            data: {
                ...req.body,
                userId: req.user?.id
            }
        });

        logInfo(`Template created: ${template.id}`);
        res.status(201).json(template);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error creating template' });
    }
};

// Get all templates for a user
export const getTemplates = async (req: AuthRequest, res: Response) => {
    try {
        const templates = await prisma.template.findMany({
            where: { userId: req.user?.id },
            include: {
                _count: {
                    select: {
                        campaigns: true
                    }
                }
            }
        });

        res.json(templates);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error fetching templates' });
    }
};

// Get a specific template
export const getTemplate = async (req: AuthRequest, res: Response) => {
    try {
        const template = await prisma.template.findUnique({
            where: { id: req.params.id },
            include: {
                _count: {
                    select: {
                        campaigns: true
                    }
                }
            }
        });

        if (!template) {
            return res.status(404).json({ message: 'Template not found' });
        }

        if (template.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        res.json(template);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error fetching template' });
    }
};

// Update a template
export const updateTemplate = async (req: AuthRequest, res: Response) => {
    try {
        const template = await prisma.template.findUnique({
            where: { id: req.params.id }
        });

        if (!template) {
            return res.status(404).json({ message: 'Template not found' });
        }

        if (template.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        const updatedTemplate = await prisma.template.update({
            where: { id: req.params.id },
            data: req.body
        });

        logInfo(`Template updated: ${updatedTemplate.id}`);
        res.json(updatedTemplate);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error updating template' });
    }
};

// Delete a template
export const deleteTemplate = async (req: AuthRequest, res: Response) => {
    try {
        const template = await prisma.template.findUnique({
            where: { id: req.params.id },
            include: {
                _count: {
                    select: {
                        campaigns: true
                    }
                }
            }
        });

        if (!template) {
            return res.status(404).json({ message: 'Template not found' });
        }

        if (template.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        // Check if template is being used in any campaigns
        if (template._count.campaigns > 0) {
            return res.status(400).json({ message: 'Cannot delete template that is being used in campaigns' });
        }

        await prisma.template.delete({
            where: { id: req.params.id }
        });

        logInfo(`Template deleted: ${req.params.id}`);
        res.status(204).send();
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error deleting template' });
    }
}; 