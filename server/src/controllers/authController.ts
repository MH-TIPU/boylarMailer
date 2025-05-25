import { Request, Response } from 'express';
import bcrypt from 'bcrypt';
import jwt, { SignOptions } from 'jsonwebtoken';
import { prisma } from '../config/database';
import { logError, logInfo } from '../services/loggingService';
import { transporter } from '../services/queueService';
import { AuthRequest } from '../types/express';

const JWT_SECRET = process.env.JWT_SECRET || 'your-secret-key';

const signToken = (payload: object): string => {
    return jwt.sign(payload, JWT_SECRET, { expiresIn: '24h' });
};

// Register a new user
export const register = async (req: Request, res: Response) => {
    try {
        const { email, password, name } = req.body;

        const existingUser = await prisma.user.findUnique({
            where: { email }
        });

        if (existingUser) {
            return res.status(400).json({ message: 'User already exists' });
        }

        const hashedPassword = await bcrypt.hash(password, 10);

        const user = await prisma.user.create({
            data: {
                email,
                password: hashedPassword,
                name,
                role: 'USER'
            }
        });

        const token = signToken({ id: user.id, email: user.email, role: user.role });

        logInfo(`User ${user.id} registered`);
        res.status(201).json({ token });
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error registering user' });
    }
};

// Login user
export const login = async (req: Request, res: Response) => {
    try {
        const { email, password } = req.body;

        const user = await prisma.user.findUnique({
            where: { email }
        });

        if (!user || !(await bcrypt.compare(password, user.password))) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        const token = signToken({ id: user.id, email: user.email, role: user.role });

        logInfo(`User ${user.id} logged in`);
        res.json({ token });
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error logging in' });
    }
};

// Request password reset
export const requestPasswordReset = async (req: Request, res: Response) => {
    try {
        const { email } = req.body;

        const user = await prisma.user.findUnique({
            where: { email }
        });

        if (!user) {
            return res.status(404).json({ message: 'User not found' });
        }

        // Generate reset token
        const resetToken = jwt.sign(
            { id: user.id },
            process.env.JWT_SECRET as jwt.Secret,
            { expiresIn: '1h' }
        );

        // Save reset token to user
        await prisma.user.update({
            where: { id: user.id },
            data: { resetToken }
        });

        // Send reset email
        const resetUrl = `${process.env.CLIENT_URL}/reset-password?token=${resetToken}`;
        await transporter.sendMail({
            to: user.email,
            subject: 'Password Reset Request',
            html: `
                <p>You requested a password reset</p>
                <p>Click this <a href="${resetUrl}">link</a> to reset your password</p>
                <p>If you didn't request this, please ignore this email</p>
            `
        });

        logInfo(`Password reset requested for user: ${user.id}`);
        res.json({ message: 'Password reset email sent' });
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error requesting password reset' });
    }
};

// Reset password
export const resetPassword = async (req: Request, res: Response) => {
    try {
        const { token, password } = req.body;

        // Verify token
        const decoded = jwt.verify(token, process.env.JWT_SECRET as jwt.Secret) as { id: string };

        // Find user
        const user = await prisma.user.findUnique({
            where: { id: decoded.id }
        });

        if (!user) {
            return res.status(404).json({ message: 'User not found' });
        }

        // Hash new password
        const salt = await bcrypt.genSalt(10);
        const hashedPassword = await bcrypt.hash(password, salt);

        // Update password and clear reset token
        await prisma.user.update({
            where: { id: user.id },
            data: {
                password: hashedPassword,
                resetToken: null
            }
        });

        logInfo(`Password reset for user: ${user.id}`);
        res.json({ message: 'Password reset successful' });
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error resetting password' });
    }
};

// Get current user
export const getCurrentUser = async (req: AuthRequest, res: Response) => {
    try {
        const user = await prisma.user.findUnique({
            where: { id: req.user?.id }
        });

        if (!user) {
            return res.status(404).json({ message: 'User not found' });
        }

        res.json({
            id: user.id,
            email: user.email,
            name: user.name,
            role: user.role
        });
    } catch (error) {
        logError(error as Error);
        res.status(500).json({ message: 'Error fetching user' });
    }
}; 