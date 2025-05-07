import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import {
    Box,
    Button,
    Container,
    TextField,
    Typography,
    Paper,
    FormControl,
    InputLabel,
    Select,
    MenuItem,
    SelectChangeEvent,
    CircularProgress,
    Alert,
    Snackbar,
} from '@mui/material';
import axios from 'axios';

interface CampaignFormData {
    name: string;
    subject: string;
    templateId: string;
    subscriberListId: string;
    content: string;
}

interface Template {
    id: string;
    name: string;
}

interface SubscriberList {
    id: string;
    name: string;
}

const CampaignForm: React.FC = () => {
    const navigate = useNavigate();
    const { id } = useParams();
    const [formData, setFormData] = useState<CampaignFormData>({
        name: '',
        subject: '',
        templateId: '',
        subscriberListId: '',
        content: '',
    });
    const [templates, setTemplates] = useState<Template[]>([]);
    const [subscriberLists, setSubscriberLists] = useState<SubscriberList[]>([]);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [templatesRes, listsRes] = await Promise.all([
                    axios.get('/api/templates'),
                    axios.get('/api/subscriber-lists'),
                ]);
                setTemplates(templatesRes.data);
                setSubscriberLists(listsRes.data);

                if (id) {
                    const campaignRes = await axios.get(`/api/campaigns/${id}`);
                    setFormData(campaignRes.data);
                }
            } catch (err) {
                setError('Failed to load form data');
                console.error('Error fetching data:', err);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, [id]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement> | SelectChangeEvent) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        setError(null);

        try {
            if (id) {
                await axios.put(`/api/campaigns/${id}`, formData);
            } else {
                await axios.post('/api/campaigns', formData);
            }
            navigate('/campaigns');
        } catch (err) {
            setError('Failed to save campaign');
            console.error('Error saving campaign:', err);
        } finally {
            setSubmitting(false);
        }
    };

    if (loading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '60vh' }}>
                <CircularProgress />
            </Box>
        );
    }

    return (
        <Container maxWidth="md">
            <Box sx={{ mt: 4, mb: 4 }}>
                <Typography variant="h4" component="h1" gutterBottom>
                    {id ? 'Edit Campaign' : 'Create New Campaign'}
                </Typography>
                <Paper sx={{ p: 3 }}>
                    <form onSubmit={handleSubmit}>
                        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
                            <TextField
                                fullWidth
                                label="Campaign Name"
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                                required
                                disabled={submitting}
                            />
                            <TextField
                                fullWidth
                                label="Subject Line"
                                name="subject"
                                value={formData.subject}
                                onChange={handleChange}
                                required
                                disabled={submitting}
                            />
                            <FormControl fullWidth>
                                <InputLabel>Template</InputLabel>
                                <Select
                                    name="templateId"
                                    value={formData.templateId}
                                    onChange={handleChange}
                                    required
                                    disabled={submitting}
                                >
                                    {templates.map((template) => (
                                        <MenuItem key={template.id} value={template.id}>
                                            {template.name}
                                        </MenuItem>
                                    ))}
                                </Select>
                            </FormControl>
                            <FormControl fullWidth>
                                <InputLabel>Subscriber List</InputLabel>
                                <Select
                                    name="subscriberListId"
                                    value={formData.subscriberListId}
                                    onChange={handleChange}
                                    required
                                    disabled={submitting}
                                >
                                    {subscriberLists.map((list) => (
                                        <MenuItem key={list.id} value={list.id}>
                                            {list.name}
                                        </MenuItem>
                                    ))}
                                </Select>
                            </FormControl>
                            <TextField
                                fullWidth
                                multiline
                                rows={4}
                                label="Content"
                                name="content"
                                value={formData.content}
                                onChange={handleChange}
                                required
                                disabled={submitting}
                            />
                            <Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end' }}>
                                <Button
                                    variant="outlined"
                                    onClick={() => navigate('/campaigns')}
                                    disabled={submitting}
                                >
                                    Cancel
                                </Button>
                                <Button
                                    type="submit"
                                    variant="contained"
                                    color="primary"
                                    disabled={submitting}
                                >
                                    {submitting ? (
                                        <CircularProgress size={24} color="inherit" />
                                    ) : (
                                        id ? 'Update Campaign' : 'Create Campaign'
                                    )}
                                </Button>
                            </Box>
                        </Box>
                    </form>
                </Paper>
            </Box>

            <Snackbar
                open={!!error}
                autoHideDuration={6000}
                onClose={() => setError(null)}
                anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
            >
                <Alert onClose={() => setError(null)} severity="error" sx={{ width: '100%' }}>
                    {error}
                </Alert>
            </Snackbar>
        </Container>
    );
};

export default CampaignForm; 