import { Request, Response, NextFunction } from 'express';
import Joi from 'joi';

export const validateCampaign = (req: Request, res: Response, next: NextFunction) => {
    const schema = Joi.object({
        name: Joi.string().required().min(3).max(100),
        subject: Joi.string().required().min(3).max(200),
        templateId: Joi.string().required().uuid(),
        subscriberListId: Joi.string().required().uuid(),
        content: Joi.string().required().min(10),
    });

    const { error } = schema.validate(req.body);
    if (error) {
        return res.status(400).json({
            status: 'error',
            message: 'Validation error',
            errors: error.details.map(detail => ({
                field: detail.path.join('.'),
                message: detail.message,
            })),
        });
    }

    next();
};

export const validateTemplate = (req: Request, res: Response, next: NextFunction) => {
    const schema = Joi.object({
        name: Joi.string().required().min(3).max(100),
        subject: Joi.string().required().min(3).max(200),
        content: Joi.string().required().min(10),
    });

    const { error } = schema.validate(req.body);
    if (error) {
        return res.status(400).json({
            status: 'error',
            message: 'Validation error',
            errors: error.details.map(detail => ({
                field: detail.path.join('.'),
                message: detail.message,
            })),
        });
    }

    next();
};

export const validateSubscriberList = (req: Request, res: Response, next: NextFunction) => {
    const schema = Joi.object({
        name: Joi.string().required().min(3).max(100),
        description: Joi.string().max(500),
    });

    const { error } = schema.validate(req.body);
    if (error) {
        return res.status(400).json({
            status: 'error',
            message: 'Validation error',
            errors: error.details.map(detail => ({
                field: detail.path.join('.'),
                message: detail.message,
            })),
        });
    }

    next();
};

export const validateSubscriber = (req: Request, res: Response, next: NextFunction) => {
    const schema = Joi.object({
        email: Joi.string().required().email(),
        firstName: Joi.string().max(100),
        lastName: Joi.string().max(100),
        metadata: Joi.object(),
    });

    const { error } = schema.validate(req.body);
    if (error) {
        return res.status(400).json({
            status: 'error',
            message: 'Validation error',
            errors: error.details.map(detail => ({
                field: detail.path.join('.'),
                message: detail.message,
            })),
        });
    }

    next();
}; 