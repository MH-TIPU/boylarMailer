import { Request, Response } from 'express';
import { prisma } from '../config/database';
import { logError, logInfo } from '../services/loggingService';
import { parse } from 'csv-parse';
import { stringify } from 'csv-stringify';
import { Readable } from 'stream';
import { RequestHandler } from 'express';
import multer from 'multer';
import { AuthRequest } from '../types/express';

interface FileRequest extends AuthRequest {
    file?: Express.Multer.File;
}

// Create a new subscriber list
export const createSubscriberList = async (req: AuthRequest, res: Response) => {
    try {
        const { name } = req.body;
        const userId = req.user?.id;

        if (!userId) {
            return res.status(401).json({ message: 'User not authenticated' });
        }

        const subscriberList = await prisma.subscriberList.create({
            data: {
                name,
                userId
            }
        });

        logInfo(`Created subscriber list ${subscriberList.id}`);
        res.status(201).json(subscriberList);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error creating subscriber list' });
    }
};

// Get all subscriber lists for the authenticated user
export const getSubscriberLists = async (req: AuthRequest, res: Response) => {
    try {
        const subscriberLists = await prisma.subscriberList.findMany({
            where: { userId: req.user?.id },
            include: {
                _count: {
                    select: { subscribers: true }
                }
            }
        });

        res.json(subscriberLists);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error getting subscriber lists' });
    }
};

// Get a specific subscriber list
export const getSubscriberList = async (req: AuthRequest, res: Response) => {
    try {
        const subscriberList = await prisma.subscriberList.findUnique({
            where: { id: req.params.id },
            include: {
                subscribers: true,
                _count: {
                    select: { subscribers: true }
                }
            }
        });

        if (!subscriberList) {
            return res.status(404).json({ message: 'Subscriber list not found' });
        }

        if (subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        res.json(subscriberList);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error getting subscriber list' });
    }
};

// Update a subscriber list
export const updateSubscriberList = async (req: AuthRequest, res: Response) => {
    try {
        const { name } = req.body;

        const subscriberList = await prisma.subscriberList.findUnique({
            where: { id: req.params.id }
        });

        if (!subscriberList) {
            return res.status(404).json({ message: 'Subscriber list not found' });
        }

        if (subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        const updatedSubscriberList = await prisma.subscriberList.update({
            where: { id: req.params.id },
            data: { name }
        });

        logInfo(`Updated subscriber list ${updatedSubscriberList.id}`);
        res.json(updatedSubscriberList);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error updating subscriber list' });
    }
};

// Delete a subscriber list
export const deleteSubscriberList = async (req: AuthRequest, res: Response) => {
    try {
        const subscriberList = await prisma.subscriberList.findUnique({
            where: { id: req.params.id }
        });

        if (!subscriberList) {
            return res.status(404).json({ message: 'Subscriber list not found' });
        }

        if (subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        await prisma.subscriberList.delete({
            where: { id: req.params.id }
        });

        logInfo(`Deleted subscriber list ${subscriberList.id}`);
        res.json({ message: 'Subscriber list deleted successfully' });
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error deleting subscriber list' });
    }
};

// Configure multer for file upload
const upload = multer({
    storage: multer.memoryStorage(),
    limits: {
        fileSize: 5 * 1024 * 1024 // 5MB limit
    },
    fileFilter: (req, file, cb) => {
        if (file.mimetype === 'text/csv') {
            cb(null, true);
        } else {
            cb(new Error('Only CSV files are allowed'));
        }
    }
});

// Import subscribers from CSV
export const importSubscribers = async (req: FileRequest, res: Response) => {
    try {
        const subscriberList = await prisma.subscriberList.findUnique({
            where: { id: req.params.id }
        });

        if (!subscriberList) {
            return res.status(404).json({ message: 'Subscriber list not found' });
        }

        if (subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        if (!req.file) {
            return res.status(400).json({ message: 'No file uploaded' });
        }

        const parser = parse({
            columns: true,
            skip_empty_lines: true
        });

        const subscribers: any[] = [];
        let errorCount = 0;

        parser.on('readable', async () => {
            let record;
            while ((record = parser.read())) {
                try {
                    const subscriber = await prisma.subscriber.create({
                        data: {
                            email: record.email,
                            firstName: record.firstName,
                            lastName: record.lastName,
                            subscriberListId: subscriberList.id
                        }
                    });
                    subscribers.push(subscriber);
                } catch (error) {
                    errorCount++;
                    logError(error as Error);
                }
            }
        });

        const readStream = new Readable();
        readStream.push(req.file.buffer);
        readStream.push(null);
        readStream.pipe(parser);

        await new Promise((resolve) => parser.on('end', resolve));

        logInfo(`Imported ${subscribers.length} subscribers to list ${subscriberList.id}`);
        res.json({
            message: `Imported ${subscribers.length} subscribers successfully`,
            errors: errorCount
        });
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error importing subscribers' });
    }
};

// Export subscribers to CSV
export const exportSubscribers = async (req: AuthRequest, res: Response) => {
    try {
        const subscriberList = await prisma.subscriberList.findUnique({
            where: { id: req.params.id },
            include: { subscribers: true }
        });

        if (!subscriberList) {
            return res.status(404).json({ message: 'Subscriber list not found' });
        }

        if (subscriberList.userId !== req.user?.id) {
            return res.status(403).json({ message: 'Not authorized' });
        }

        const csvData = subscriberList.subscribers.map(subscriber => ({
            email: subscriber.email,
            firstName: subscriber.firstName,
            lastName: subscriber.lastName
        }));

        res.setHeader('Content-Type', 'text/csv');
        res.setHeader('Content-Disposition', `attachment; filename=subscribers-${subscriberList.id}.csv`);

        const csv = csvData.map(row => Object.values(row).join(',')).join('\n');
        res.send(csv);

        logInfo(`Exported ${csvData.length} subscribers from list ${subscriberList.id}`);
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error exporting subscribers' });
    }
}; 