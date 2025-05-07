import React, { useState } from 'react';
import {
    Box,
    Button,
    Card,
    CardContent,
    Typography,
    IconButton,
    Chip,
    Stack,
    CircularProgress,
    Alert,
    Snackbar,
} from '@mui/material';
import {
    Add as AddIcon,
    Edit as EditIcon,
    Delete as DeleteIcon,
    Send as SendIcon,
    Schedule as ScheduleIcon,
} from '@mui/icons-material';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

interface Campaign {
    id: string;
    name: string;
    subject: string;
    status: 'draft' | 'scheduled' | 'sending' | 'sent' | 'failed';
    status_badge: 'warning' | 'info' | 'primary' | 'success' | 'error';
    scheduled_at?: string;
    subscriber_list?: {
        name: string;
    };
}

interface CampaignsProps {
    campaigns: Campaign[];
    loading: boolean;
}

const Campaigns: React.FC<CampaignsProps> = ({ campaigns, loading }) => {
    const navigate = useNavigate();
    const [error, setError] = useState<string | null>(null);
    const [actionLoading, setActionLoading] = useState<string | null>(null);

    if (loading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '60vh' }}>
                <CircularProgress />
            </Box>
        );
    }

    const handleDelete = async (id: string) => {
        if (!window.confirm('Are you sure you want to delete this campaign?')) {
            return;
        }

        setActionLoading(id);
        try {
            await axios.delete(`/api/campaigns/${id}`);
            window.location.reload();
        } catch (error) {
            setError('Failed to delete campaign');
            console.error('Error deleting campaign:', error);
        } finally {
            setActionLoading(null);
        }
    };

    const handleSend = async (id: string) => {
        if (!window.confirm('Are you sure you want to send this campaign?')) {
            return;
        }

        setActionLoading(id);
        try {
            await axios.post(`/api/campaigns/${id}/send`);
            window.location.reload();
        } catch (error) {
            setError('Failed to send campaign');
            console.error('Error sending campaign:', error);
        } finally {
            setActionLoading(null);
        }
    };

    const handleSchedule = async (id: string) => {
        const scheduledAt = prompt('Enter schedule date and time (YYYY-MM-DD HH:mm):');
        if (!scheduledAt) return;

        setActionLoading(id);
        try {
            await axios.post(`/api/campaigns/${id}/schedule`, {
                scheduled_at: scheduledAt,
            });
            window.location.reload();
        } catch (error) {
            setError('Failed to schedule campaign');
            console.error('Error scheduling campaign:', error);
        } finally {
            setActionLoading(null);
        }
    };

    const handleCancel = async (id: string) => {
        if (!window.confirm('Are you sure you want to cancel this scheduled campaign?')) {
            return;
        }

        setActionLoading(id);
        try {
            await axios.post(`/api/campaigns/${id}/cancel`);
            window.location.reload();
        } catch (error) {
            setError('Failed to cancel campaign');
            console.error('Error cancelling campaign:', error);
        } finally {
            setActionLoading(null);
        }
    };

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
                <Typography variant="h4">Email Campaigns</Typography>
                <Button
                    variant="contained"
                    startIcon={<AddIcon />}
                    onClick={() => navigate('/campaigns/new')}
                >
                    Create Campaign
                </Button>
            </Box>

            <Box sx={{ 
                display: 'grid', 
                gridTemplateColumns: {
                    xs: '1fr',
                    md: 'repeat(2, 1fr)',
                    lg: 'repeat(3, 1fr)'
                },
                gap: 3 
            }}>
                {campaigns.map((campaign) => (
                    <Card key={campaign.id}>
                        <CardContent>
                            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                <Typography variant="h6" gutterBottom>
                                    {campaign.name}
                                </Typography>
                                <Chip
                                    label={campaign.status}
                                    color={campaign.status_badge}
                                    size="small"
                                />
                            </Box>
                            <Typography color="textSecondary" gutterBottom>
                                {campaign.subject}
                            </Typography>
                            <Typography variant="body2" color="textSecondary" gutterBottom>
                                List: {campaign.subscriber_list?.name}
                            </Typography>
                            {campaign.scheduled_at && (
                                <Typography variant="body2" color="textSecondary">
                                    Scheduled: {new Date(campaign.scheduled_at).toLocaleString()}
                                </Typography>
                            )}
                            <Stack direction="row" spacing={1} sx={{ mt: 2 }}>
                                {campaign.status === 'draft' && (
                                    <>
                                        <Button
                                            size="small"
                                            startIcon={<EditIcon />}
                                            onClick={() => navigate(`/campaigns/${campaign.id}/edit`)}
                                            disabled={actionLoading === campaign.id}
                                        >
                                            Edit
                                        </Button>
                                        <Button
                                            size="small"
                                            startIcon={<SendIcon />}
                                            onClick={() => handleSend(campaign.id)}
                                            disabled={actionLoading === campaign.id}
                                        >
                                            Send
                                        </Button>
                                        <Button
                                            size="small"
                                            startIcon={<ScheduleIcon />}
                                            onClick={() => handleSchedule(campaign.id)}
                                            disabled={actionLoading === campaign.id}
                                        >
                                            Schedule
                                        </Button>
                                        <IconButton
                                            size="small"
                                            onClick={() => handleDelete(campaign.id)}
                                            disabled={actionLoading === campaign.id}
                                        >
                                            <DeleteIcon />
                                        </IconButton>
                                    </>
                                )}
                                {campaign.status === 'scheduled' && (
                                    <Button
                                        size="small"
                                        color="error"
                                        onClick={() => handleCancel(campaign.id)}
                                        disabled={actionLoading === campaign.id}
                                    >
                                        Cancel
                                    </Button>
                                )}
                                {campaign.status === 'sent' && (
                                    <Button
                                        size="small"
                                        onClick={() => navigate(`/campaigns/${campaign.id}`)}
                                        disabled={actionLoading === campaign.id}
                                    >
                                        View Stats
                                    </Button>
                                )}
                            </Stack>
                        </CardContent>
                    </Card>
                ))}
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
        </Box>
    );
};

export default Campaigns; 