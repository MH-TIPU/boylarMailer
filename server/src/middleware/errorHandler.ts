import { Request, Response, NextFunction } from 'express';
import { ValidationError } from 'joi';
import { JsonWebTokenError } from 'jsonwebtoken';
import { logError } from '../services/loggingService';

export class AppError extends Error {
    statusCode: number;
    status: string;
    isOperational: boolean;

    constructor(message: string, statusCode: number) {
        super(message);
        this.statusCode = statusCode;
        this.status = `${statusCode}`.startsWith('4') ? 'fail' : 'error';
        this.isOperational = true;

        Error.captureStackTrace(this, this.constructor);
    }
}

export const errorHandler = (
    err: Error | AppError,
    req: Request,
    res: Response,
    next: NextFunction
) => {
    if (err instanceof AppError) {
        logError(err);
        return res.status(err.statusCode).json({
            status: err.status,
            message: err.message
        });
    }

    // Log unexpected errors
    logError(err);

    // Send generic error response
    res.status(500).json({
        status: 'error',
        message: 'Something went wrong'
    });
}; 