import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';

// Template validation schema
const templateSchema = z.object({
    name: z.string().min(1, 'Name is required'),
    subject: z.string().min(1, 'Subject is required'),
    content: z.string().min(1, 'Content is required')
});

// Campaign validation schema
const campaignSchema = z.object({
    name: z.string().min(1, 'Name is required'),
    templateId: z.string().uuid('Invalid template ID'),
    subscriberListId: z.string().uuid('Invalid subscriber list ID'),
    schedule: z.object({
        startDate: z.string().datetime('Invalid start date'),
        endDate: z.string().datetime('Invalid end date').optional(),
        timezone: z.string().min(1, 'Timezone is required')
    }).optional()
});

// Subscriber list validation schema
const subscriberListSchema = z.object({
    name: z.string().min(1, 'Name is required'),
    description: z.string().optional()
});

// Subscriber validation schema
const subscriberSchema = z.object({
    email: z.string().email('Invalid email'),
    firstName: z.string().optional(),
    lastName: z.string().optional(),
    subscriberListId: z.string().uuid('Invalid subscriber list ID')
});

// Validation middleware factory
const validate = (schema: z.ZodSchema) => {
    return async (req: Request, res: Response, next: NextFunction) => {
        try {
            await schema.parseAsync(req.body);
            next();
        } catch (error) {
            if (error instanceof z.ZodError) {
                return res.status(400).json({
                    message: 'Validation error',
                    errors: error.errors
                });
            }
            next(error);
        }
    };
};

// Export validation middleware
export const validateTemplate = validate(templateSchema);
export const validateCampaign = validate(campaignSchema);
export const validateSubscriberList = validate(subscriberListSchema);
export const validateSubscriber = validate(subscriberSchema); 