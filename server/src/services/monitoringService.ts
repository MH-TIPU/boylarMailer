import { createClient } from 'prom-client';
import { Request, Response } from 'express';

// Create a Registry to register metrics
const register = new createClient.Registry();

// Add default metrics (CPU, memory, etc.)
createClient.collectDefaultMetrics({ register });

// Create custom metrics
const httpRequestDurationMicroseconds = new createClient.Histogram({
    name: 'http_request_duration_seconds',
    help: 'Duration of HTTP requests in seconds',
    labelNames: ['method', 'route', 'status_code'],
    buckets: [0.1, 0.5, 1, 2, 5],
});

const httpRequestsTotal = new createClient.Counter({
    name: 'http_requests_total',
    help: 'Total number of HTTP requests',
    labelNames: ['method', 'route', 'status_code'],
});

const activeUsers = new createClient.Gauge({
    name: 'active_users',
    help: 'Number of active users',
});

const emailQueueSize = new createClient.Gauge({
    name: 'email_queue_size',
    help: 'Number of emails in the queue',
});

const emailSendDuration = new createClient.Histogram({
    name: 'email_send_duration_seconds',
    help: 'Duration of email sending in seconds',
    buckets: [0.1, 0.5, 1, 2, 5],
});

// Register custom metrics
register.registerMetric(httpRequestDurationMicroseconds);
register.registerMetric(httpRequestsTotal);
register.registerMetric(activeUsers);
register.registerMetric(emailQueueSize);
register.registerMetric(emailSendDuration);

// Middleware to track HTTP metrics
export const metricsMiddleware = (req: Request, res: Response, next: Function) => {
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

// Update active users count
export const updateActiveUsers = (count: number) => {
    activeUsers.set(count);
};

// Update email queue size
export const updateEmailQueueSize = (size: number) => {
    emailQueueSize.set(size);
};

// Track email send duration
export const trackEmailSend = (duration: number) => {
    emailSendDuration.observe(duration / 1000);
};

// Get metrics endpoint handler
export const getMetrics = async (req: Request, res: Response) => {
    res.set('Content-Type', register.contentType);
    res.end(await register.metrics());
};

export default {
    metricsMiddleware,
    updateActiveUsers,
    updateEmailQueueSize,
    trackEmailSend,
    getMetrics,
}; 