import Queue from 'bull';
import nodemailer from 'nodemailer';
import { prisma } from '../config/database';
import { logError, logInfo } from './loggingService';

interface EmailJob {
    campaignId: string;
    subscriberId: string;
    email: string;
    subject: string;
    content: string;
}

// Create Redis connection config
const redisConfig = {
    host: process.env.REDIS_HOST || 'localhost',
    port: parseInt(process.env.REDIS_PORT || '6379'),
    password: process.env.REDIS_PASSWORD,
};

// Create email queue
export const emailQueue = new Queue<EmailJob>('email-queue', {
    redis: redisConfig,
    defaultJobOptions: {
        attempts: 3,
        backoff: {
            type: 'exponential',
            delay: 1000,
        },
    },
});

// Create email transporter
const transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST,
    port: parseInt(process.env.SMTP_PORT || '587'),
    secure: process.env.SMTP_SECURE === 'true',
    auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS,
    },
});

// Process jobs
emailQueue.process(async (job) => {
    const { campaignId, subscriberId, email, subject, content } = job.data;

    try {
        // Send email
        await transporter.sendMail({
            from: process.env.SMTP_FROM,
            to: email,
            subject: subject,
            html: content,
            headers: {
                'X-Campaign-ID': campaignId,
                'X-Subscriber-ID': subscriberId,
            },
        });

        // Record email sent
        await prisma.emailSent.create({
            data: {
                campaignId,
                subscriberId,
            },
        });

        logInfo(`Email sent to ${email} for campaign ${campaignId}`);
    } catch (error) {
        logError(error as Error);
        throw error;
    }
});

// Handle failed jobs
emailQueue.on('failed', async (job, error) => {
    const { campaignId, subscriberId } = job.data;
    logError(error);

    // Update campaign statistics
    await prisma.campaign.update({
        where: { id: campaignId },
        data: {
            status: 'FAILED',
        },
    });
});

// Get queue status
export const getQueueStatus = async () => {
    const [waiting, active, completed, failed] = await Promise.all([
        emailQueue.getWaitingCount(),
        emailQueue.getActiveCount(),
        emailQueue.getCompletedCount(),
        emailQueue.getFailedCount(),
    ]);

    return {
        waiting,
        active,
        completed,
        failed,
    };
}; 