import React from 'react';
import {
    Box,
    Card,
    CardContent,
    Typography,
    Grid,
    Button,
    Divider,
    List,
    ListItem,
    ListItemText,
    Chip,
} from '@mui/material';
import {
    ArrowBack as ArrowBackIcon,
    Send as SendIcon,
    Schedule as ScheduleIcon,
} from '@mui/icons-material';
import { router } from '@inertiajs/react';
import axios from 'axios';

const CampaignShow = ({ campaign }) => {
    const handleSend = async () => {
        try {
            await axios.post(`/api/campaigns/${campaign.id}/send`);
            router.reload();
        } catch (error) {
            console.error('Error sending campaign:', error);
        }
    };

    const handleSchedule = async () => {
        const scheduledAt = prompt('Enter schedule date and time (YYYY-MM-DD HH:mm):');
        if (!scheduledAt) return;

        try {
            await axios.post(`/api/campaigns/${campaign.id}/schedule`, {
                scheduled_at: scheduledAt,
            });
            router.reload();
        } catch (error) {
            console.error('Error scheduling campaign:', error);
        }
    };

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ display: 'flex', alignItems: 'center', mb: 3 }}>
                <Button
                    startIcon={<ArrowBackIcon />}
                    onClick={() => router.visit('/campaigns')}
                    sx={{ mr: 2 }}
                >
                    Back to Campaigns
                </Button>
                <Typography variant="h4">{campaign.name}</Typography>
            </Box>

            <Grid container spacing={3}>
                <Grid item xs={12} md={8}>
                    <Card>
                        <CardContent>
                            <Typography variant="h6" gutterBottom>
                                Campaign Details
                            </Typography>
                            <Box sx={{ mb: 2 }}>
                                <Typography variant="subtitle1" color="textSecondary">
                                    Subject
                                </Typography>
                                <Typography variant="body1">{campaign.subject}</Typography>
                            </Box>
                            <Box sx={{ mb: 2 }}>
                                <Typography variant="subtitle1" color="textSecondary">
                                    Subscriber List
                                </Typography>
                                <Typography variant="body1">
                                    {campaign.subscriber_list?.name}
                                </Typography>
                            </Box>
                            <Box sx={{ mb: 2 }}>
                                <Typography variant="subtitle1" color="textSecondary">
                                    Status
                                </Typography>
                                <Chip
                                    label={campaign.status}
                                    color={campaign.status_badge}
                                    size="small"
                                />
                            </Box>
                            {campaign.scheduled_at && (
                                <Box sx={{ mb: 2 }}>
                                    <Typography variant="subtitle1" color="textSecondary">
                                        Scheduled For
                                    </Typography>
                                    <Typography variant="body1">
                                        {new Date(campaign.scheduled_at).toLocaleString()}
                                    </Typography>
                                </Box>
                            )}
                            <Divider sx={{ my: 2 }} />
                            <Typography variant="subtitle1" color="textSecondary" gutterBottom>
                                Email Content
                            </Typography>
                            <Box
                                sx={{
                                    p: 2,
                                    bgcolor: 'grey.100',
                                    borderRadius: 1,
                                    '& img': { maxWidth: '100%' },
                                }}
                                dangerouslySetInnerHTML={{ __html: campaign.content }}
                            />
                        </CardContent>
                    </Card>
                </Grid>

                <Grid item xs={12} md={4}>
                    <Card>
                        <CardContent>
                            <Typography variant="h6" gutterBottom>
                                Campaign Statistics
                            </Typography>
                            <List>
                                <ListItem>
                                    <ListItemText
                                        primary="Total Recipients"
                                        secondary={campaign.total_recipients || 0}
                                    />
                                </ListItem>
                                <ListItem>
                                    <ListItemText
                                        primary="Opened"
                                        secondary={`${campaign.open_rate || 0}%`}
                                    />
                                </ListItem>
                                <ListItem>
                                    <ListItemText
                                        primary="Clicked"
                                        secondary={`${campaign.click_rate || 0}%`}
                                    />
                                </ListItem>
                                <ListItem>
                                    <ListItemText
                                        primary="Bounced"
                                        secondary={`${campaign.bounce_rate || 0}%`}
                                    />
                                </ListItem>
                                <ListItem>
                                    <ListItemText
                                        primary="Unsubscribed"
                                        secondary={`${campaign.unsubscribe_rate || 0}%`}
                                    />
                                </ListItem>
                            </List>

                            {campaign.status === 'draft' && (
                                <Box sx={{ mt: 2 }}>
                                    <Button
                                        fullWidth
                                        variant="contained"
                                        startIcon={<SendIcon />}
                                        onClick={handleSend}
                                        sx={{ mb: 1 }}
                                    >
                                        Send Now
                                    </Button>
                                    <Button
                                        fullWidth
                                        variant="outlined"
                                        startIcon={<ScheduleIcon />}
                                        onClick={handleSchedule}
                                    >
                                        Schedule
                                    </Button>
                                </Box>
                            )}
                        </CardContent>
                    </Card>
                </Grid>
            </Grid>
        </Box>
    );
};

export default CampaignShow; 