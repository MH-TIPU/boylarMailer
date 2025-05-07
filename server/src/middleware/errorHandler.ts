import { Request, Response, NextFunction } from 'express';
import { ValidationError } from 'joi';
import { JsonWebTokenError } from 'jsonwebtoken';
import { QueryFailedError } from 'typeorm';

interface AppError extends Error {
    statusCode?: number;
    code?: string;
}

export const errorHandler = (
    err: AppError,
    req: Request,
    res: Response,
    next: NextFunction
) => {
    console.error('Error:', {
        message: err.message,
        stack: err.stack,
        path: req.path,
        method: req.method,
        body: req.body,
        query: req.query,
        params: req.params,
        user: req.user,
    });

    // Handle Joi validation errors
    if (err instanceof ValidationError) {
        return res.status(400).json({
            status: 'error',
            message: 'Validation error',
            errors: err.details.map(detail => ({
                field: detail.path.join('.'),
                message: detail.message,
            })),
        });
    }

    // Handle JWT errors
    if (err instanceof JsonWebTokenError) {
        return res.status(401).json({
            status: 'error',
            message: 'Invalid token',
        });
    }

    // Handle database errors
    if (err instanceof QueryFailedError) {
        // Handle unique constraint violations
        if (err.message.includes('duplicate key')) {
            return res.status(409).json({
                status: 'error',
                message: 'Resource already exists',
            });
        }

        return res.status(500).json({
            status: 'error',
            message: 'Database error',
        });
    }

    // Handle custom application errors
    if (err.statusCode) {
        return res.status(err.statusCode).json({
            status: 'error',
            message: err.message,
        });
    }

    // Handle all other errors
    return res.status(500).json({
        status: 'error',
        message: 'Internal server error',
    });
}; 