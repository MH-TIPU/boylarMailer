import { Request } from 'express';
import { File } from 'multer';

export interface FileRequest extends Request {
    file?: File;
}

export interface AuthRequest extends Request {
    user?: {
        id: string;
        email: string;
        role: string;
    };
} 