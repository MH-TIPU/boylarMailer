import rateLimit from 'express-rate-limit';
import { logError } from '../services/loggingService';

export const rateLimiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100, // limit each IP to 100 requests per windowMs
    message: 'Too many requests from this IP, please try again later',
    handler: (req, res) => {
        logError(new Error(`Rate limit exceeded for IP: ${req.ip}`));
        res.status(429).json({
            message: 'Too many requests from this IP, please try again later'
        });
    }
}); 