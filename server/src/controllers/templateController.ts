import { Request, Response } from 'express';
import { EmailTemplate } from '../models/EmailTemplate';

// Create a new template
export const createTemplate = async (req: Request, res: Response) => {
  try {
    const { name, subject, content, isPublic } = req.body;
    const userId = req.user?.userId; // This will be set by auth middleware

    const template = new EmailTemplate({
      name,
      subject,
      content,
      userId,
      isPublic,
    });

    await template.save();
    res.status(201).json(template);
  } catch (error) {
    res.status(500).json({ message: 'Error creating template', error });
  }
};

// Get all templates for a user
export const getTemplates = async (req: Request, res: Response) => {
  try {
    const userId = req.user?.userId;
    const templates = await EmailTemplate.find({
      $or: [
        { userId },
        { isPublic: true }
      ]
    }).sort({ createdAt: -1 });
    res.json(templates);
  } catch (error) {
    res.status(500).json({ message: 'Error fetching templates', error });
  }
};

// Get a single template
export const getTemplate = async (req: Request, res: Response) => {
  try {
    const template = await EmailTemplate.findOne({
      _id: req.params.id,
      $or: [
        { userId: req.user?.userId },
        { isPublic: true }
      ]
    });

    if (!template) {
      return res.status(404).json({ message: 'Template not found' });
    }

    res.json(template);
  } catch (error) {
    res.status(500).json({ message: 'Error fetching template', error });
  }
};

// Update a template
export const updateTemplate = async (req: Request, res: Response) => {
  try {
    const { name, subject, content, isPublic } = req.body;
    const template = await EmailTemplate.findOneAndUpdate(
      { _id: req.params.id, userId: req.user?.userId },
      { name, subject, content, isPublic },
      { new: true }
    );

    if (!template) {
      return res.status(404).json({ message: 'Template not found' });
    }

    res.json(template);
  } catch (error) {
    res.status(500).json({ message: 'Error updating template', error });
  }
};

// Delete a template
export const deleteTemplate = async (req: Request, res: Response) => {
  try {
    const template = await EmailTemplate.findOneAndDelete({
      _id: req.params.id,
      userId: req.user?.userId
    });

    if (!template) {
      return res.status(404).json({ message: 'Template not found' });
    }

    res.json({ message: 'Template deleted successfully' });
  } catch (error) {
    res.status(500).json({ message: 'Error deleting template', error });
  }
}; 