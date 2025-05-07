import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import {
  Container,
  Paper,
  TextField,
  Button,
  Typography,
  Box,
  Switch,
  FormControlLabel,
  CircularProgress,
} from '@mui/material';
import { templateService, Template } from '../../services/templateService';

const TemplateBuilder: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [template, setTemplate] = useState<Partial<Template>>({
    name: '',
    subject: '',
    content: '',
    isPublic: false,
  });

  useEffect(() => {
    if (id) {
      loadTemplate();
    }
  }, [id]);

  const loadTemplate = async () => {
    try {
      setLoading(true);
      const data = await templateService.getTemplate(id!);
      setTemplate(data);
    } catch (error) {
      console.error('Error loading template:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setLoading(true);
      if (id) {
        await templateService.updateTemplate(id, template);
      } else {
        await templateService.createTemplate(template as Omit<Template, '_id' | 'createdAt' | 'updatedAt'>);
      }
      navigate('/templates');
    } catch (error) {
      console.error('Error saving template:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="100vh">
        <CircularProgress />
      </Box>
    );
  }

  return (
    <Container maxWidth="lg">
      <Box sx={{ mt: 4, mb: 4 }}>
        <Paper elevation={3} sx={{ p: 4 }}>
          <Typography variant="h4" component="h1" gutterBottom>
            {id ? 'Edit Template' : 'Create New Template'}
          </Typography>
          <form onSubmit={handleSubmit}>
            <TextField
              fullWidth
              label="Template Name"
              value={template.name}
              onChange={(e) => setTemplate({ ...template, name: e.target.value })}
              margin="normal"
              required
            />
            <TextField
              fullWidth
              label="Email Subject"
              value={template.subject}
              onChange={(e) => setTemplate({ ...template, subject: e.target.value })}
              margin="normal"
              required
            />
            <TextField
              fullWidth
              label="Email Content"
              value={template.content}
              onChange={(e) => setTemplate({ ...template, content: e.target.value })}
              margin="normal"
              required
              multiline
              rows={10}
            />
            <FormControlLabel
              control={
                <Switch
                  checked={template.isPublic}
                  onChange={(e) => setTemplate({ ...template, isPublic: e.target.checked })}
                />
              }
              label="Make this template public"
            />
            <Box sx={{ mt: 3 }}>
              <Button
                type="submit"
                variant="contained"
                color="primary"
                disabled={loading}
              >
                {loading ? 'Saving...' : 'Save Template'}
              </Button>
              <Button
                variant="outlined"
                onClick={() => navigate('/templates')}
                sx={{ ml: 2 }}
              >
                Cancel
              </Button>
            </Box>
          </form>
        </Paper>
      </Box>
    </Container>
  );
};

export default TemplateBuilder; 