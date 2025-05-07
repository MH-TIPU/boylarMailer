import axios from 'axios';

const API_URL = 'http://localhost:5000/api/templates';

export interface Template {
  _id: string;
  name: string;
  subject: string;
  content: string;
  isPublic: boolean;
  createdAt: string;
  updatedAt: string;
}

export const templateService = {
  // Create a new template
  createTemplate: async (template: Omit<Template, '_id' | 'createdAt' | 'updatedAt'>) => {
    const response = await axios.post(API_URL, template);
    return response.data;
  },

  // Get all templates
  getTemplates: async () => {
    const response = await axios.get(API_URL);
    return response.data;
  },

  // Get a single template
  getTemplate: async (id: string) => {
    const response = await axios.get(`${API_URL}/${id}`);
    return response.data;
  },

  // Update a template
  updateTemplate: async (id: string, template: Partial<Template>) => {
    const response = await axios.put(`${API_URL}/${id}`, template);
    return response.data;
  },

  // Delete a template
  deleteTemplate: async (id: string) => {
    const response = await axios.delete(`${API_URL}/${id}`);
    return response.data;
  },
}; 