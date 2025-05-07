import React from 'react';
import {
    Box,
    Button,
    Card,
    CardContent,
    Typography,
    IconButton,
    Chip,
    Grid,
    Stack,
} from '@mui/material';
import {
    Add as AddIcon,
    Edit as EditIcon,
    Delete as DeleteIcon,
    Send as SendIcon,
    Schedule as ScheduleIcon,
} from '@mui/icons-material';
import { router } from '@inertiajs/react';
import axios from 'axios';

const Campaigns = ({ campaigns }) => {
    const handleDelete = async (id) => {
        if (window.confirm('Are you sure you want to delete this campaign?')) {
            try {
                await axios.delete(`/api/campaigns/${id}`);
                router.reload();
            } catch (error) {
                console.error('Error deleting campaign:', error);
            }
        }
    };

    const handleSend = async (id) => {
        try {
            await axios.post(`/api/campaigns/${id}/send`);
            router.reload();
        } catch (error) {
            console.error('Error sending campaign:', error);
        }
    };

    const handleSchedule = async (id) => {
        const scheduledAt = prompt('Enter schedule date and time (YYYY-MM-DD HH:mm):');
        if (!scheduledAt) return;

        try {
            await axios.post(`/api/campaigns/${id}/schedule`, {
                scheduled_at: scheduledAt,
            });
            router.reload();
        } catch (error) {
            console.error('Error scheduling campaign:', error);
        }
    };

    const handleCancel = async (id) => {
        try {
            await axios.post(`/api/campaigns/${id}/cancel`);
            router.reload();
        } catch (error) {
            console.error('Error cancelling campaign:', error);
        }
    };

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
                <Typography variant="h4">Email Campaigns</Typography>
                <Button
                    variant="contained"
                    startIcon={<AddIcon />}
                    onClick={() => router.visit('/campaigns/create')}
                >
                    Create Campaign
                </Button>
            </Box>

            <Grid container spacing={3}>
                {campaigns.map((campaign) => (
                    <Grid item xs={12} md={6} lg={4} key={campaign.id}>
                        <Card>
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
                                                onClick={() => router.visit(`/campaigns/${campaign.id}/edit`)}
                                            >
                                                Edit
                                            </Button>
                                            <Button
                                                size="small"
                                                startIcon={<SendIcon />}
                                                onClick={() => handleSend(campaign.id)}
                                            >
                                                Send
                                            </Button>
                                            <Button
                                                size="small"
                                                startIcon={<ScheduleIcon />}
                                                onClick={() => handleSchedule(campaign.id)}
                                            >
                                                Schedule
                                            </Button>
                                            <IconButton
                                                size="small"
                                                onClick={() => handleDelete(campaign.id)}
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
                                        >
                                            Cancel
                                        </Button>
                                    )}
                                    {campaign.status === 'sent' && (
                                        <Button
                                            size="small"
                                            onClick={() => router.visit(`/campaigns/${campaign.id}`)}
                                        >
                                            View Stats
                                        </Button>
                                    )}
                                </Stack>
                            </CardContent>
                        </Card>
                    </Grid>
                ))}
            </Grid>
        </Box>
    );
};

export default Campaigns; 