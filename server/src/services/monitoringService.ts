import { Request, Response, NextFunction } from 'express';
import client from 'prom-client';
import { logError } from './loggingService';

// Create a Registry to register metrics
const register = new client.Registry();

// Add default metrics (CPU, memory, etc.)
client.collectDefaultMetrics({ register });

// Create custom metrics
const httpRequestDurationMicroseconds = new client.Histogram({
    name: 'http_request_duration_seconds',
    help: 'Duration of HTTP requests in seconds',
    labelNames: ['method', 'route', 'status_code'],
    buckets: [0.1, 0.5, 1, 2, 5]
});

const httpRequestsTotal = new client.Counter({
    name: 'http_requests_total',
    help: 'Total number of HTTP requests',
    labelNames: ['method', 'route', 'status_code']
});

const activeUsers = new client.Gauge({
    name: 'active_users',
    help: 'Number of active users'
});

const emailQueueSize = new client.Gauge({
    name: 'email_queue_size',
    help: 'Number of emails in the queue'
});

const emailSendDuration = new client.Histogram({
    name: 'email_send_duration_seconds',
    help: 'Duration of email sending in seconds',
    labelNames: ['status'],
    buckets: [0.1, 0.5, 1, 2, 5]
});

// Register custom metrics
register.registerMetric(httpRequestDurationMicroseconds);
register.registerMetric(httpRequestsTotal);
register.registerMetric(activeUsers);
register.registerMetric(emailQueueSize);
register.registerMetric(emailSendDuration);

// Middleware to track HTTP metrics
export const middleware = (req: Request, res: Response, next: NextFunction) => {
    const start = Date.now();

    res.on('finish', () => {
        const duration = Date.now() - start;
        const route = req.route?.path || req.path;
        const statusCode = res.statusCode.toString();

        httpRequestDurationMicroseconds
            .labels(req.method, route, statusCode)
            .observe(duration / 1000);

        httpRequestsTotal
            .labels(req.method, route, statusCode)
            .inc();
    });

    next();
};

// Function to update active users count
export const updateActiveUsers = (count: number) => {
    activeUsers.set(count);
};

// Function to update email queue size
export const updateEmailQueueSize = (size: number) => {
    emailQueueSize.set(size);
};

// Function to track email send duration
export const trackEmailSend = (duration: number, status: string) => {
    emailSendDuration.labels(status).observe(duration / 1000);
};

// Metrics endpoint handler
export const metricsHandler = async (req: Request, res: Response) => {
    try {
        res.set('Content-Type', register.contentType);
        res.end(await register.metrics());
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error generating metrics' });
    }
};

export default {
    middleware,
    updateActiveUsers,
    updateEmailQueueSize,
    trackEmailSend,
    metricsHandler,
}; 