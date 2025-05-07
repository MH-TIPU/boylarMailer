import winston from 'winston';
import { Request, Response, NextFunction } from 'express';

// Define log levels
const levels = {
    error: 0,
    warn: 1,
    info: 2,
    http: 3,
    debug: 4,
};

// Define log colors
const colors = {
    error: 'red',
    warn: 'yellow',
    info: 'green',
    http: 'magenta',
    debug: 'white',
};

// Add colors to Winston
winston.addColors(colors);

// Create the logger
const logger = winston.createLogger({
    level: process.env.NODE_ENV === 'development' ? 'debug' : 'info',
    levels,
    format: winston.format.combine(
        winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss:ms' }),
        winston.format.colorize({ all: true }),
        winston.format.printf(
            (info) => `${info.timestamp} ${info.level}: ${info.message}`,
        ),
    ),
    transports: [
        new winston.transports.Console(),
        new winston.transports.File({
            filename: 'logs/error.log',
            level: 'error',
        }),
        new winston.transports.File({ filename: 'logs/all.log' }),
    ],
});

// Create a stream object for Morgan
export const stream = {
    write: (message: string) => {
        logger.http(message.trim());
    },
};

// Log HTTP requests
export const logHttpRequest = (req: Request, res: Response, next: NextFunction) => {
    const start = Date.now();

    res.on('finish', () => {
        const duration = Date.now() - start;
        logger.http(
            `${req.method} ${req.originalUrl} ${res.statusCode} ${duration}ms`,
        );
    });

    next();
};

// Log errors
export const logError = (error: Error, req?: Request) => {
    logger.error({
        message: error.message,
        stack: error.stack,
        path: req?.originalUrl,
        method: req?.method,
    });
};

// Log info messages
export const logInfo = (message: string, meta?: any) => {
    logger.info(message, meta);
};

// Log warning messages
export const logWarn = (message: string, meta?: any) => {
    logger.warn(message, meta);
};

// Log debug messages
export const logDebug = (message: string, meta?: any) => {
    logger.debug(message, meta);
};

export default {
    stream,
    logHttpRequest,
    logError,
    logInfo,
    logWarn,
    logDebug,
}; 