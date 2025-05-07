import React, { useState, useEffect } from 'react';
import {
    Box,
    Button,
    TextField,
    FormControl,
    InputLabel,
    Select,
    MenuItem,
    Typography,
    Paper,
    Grid,
} from '@mui/material';
import { router } from '@inertiajs/react';
import axios from 'axios';
import { Editor } from '@tinymce/tinymce-react';

const CampaignForm = ({ campaign, subscriberLists }) => {
    const [formData, setFormData] = useState({
        name: '',
        subject: '',
        content: '',
        subscriber_list_id: '',
        ...campaign,
    });

    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({
            ...prev,
            [name]: value,
        }));
    };

    const handleEditorChange = (content) => {
        setFormData((prev) => ({
            ...prev,
            content,
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});

        try {
            if (campaign?.id) {
                await axios.put(`/api/campaigns/${campaign.id}`, formData);
            } else {
                await axios.post('/api/campaigns', formData);
            }
            router.visit('/campaigns');
        } catch (error) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            }
        }
    };

    return (
        <Box sx={{ p: 3 }}>
            <Paper sx={{ p: 3 }}>
                <Typography variant="h4" gutterBottom>
                    {campaign?.id ? 'Edit Campaign' : 'Create Campaign'}
                </Typography>

                <form onSubmit={handleSubmit}>
                    <Grid container spacing={3}>
                        <Grid item xs={12}>
                            <TextField
                                fullWidth
                                label="Campaign Name"
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                                error={!!errors.name}
                                helperText={errors.name?.[0]}
                                required
                            />
                        </Grid>

                        <Grid item xs={12}>
                            <TextField
                                fullWidth
                                label="Email Subject"
                                name="subject"
                                value={formData.subject}
                                onChange={handleChange}
                                error={!!errors.subject}
                                helperText={errors.subject?.[0]}
                                required
                            />
                        </Grid>

                        <Grid item xs={12}>
                            <FormControl fullWidth error={!!errors.subscriber_list_id}>
                                <InputLabel>Subscriber List</InputLabel>
                                <Select
                                    name="subscriber_list_id"
                                    value={formData.subscriber_list_id}
                                    onChange={handleChange}
                                    label="Subscriber List"
                                    required
                                >
                                    {subscriberLists.map((list) => (
                                        <MenuItem key={list.id} value={list.id}>
                                            {list.name}
                                        </MenuItem>
                                    ))}
                                </Select>
                                {errors.subscriber_list_id && (
                                    <Typography color="error" variant="caption">
                                        {errors.subscriber_list_id[0]}
                                    </Typography>
                                )}
                            </FormControl>
                        </Grid>

                        <Grid item xs={12}>
                            <Typography variant="subtitle1" gutterBottom>
                                Email Content
                            </Typography>
                            <Editor
                                apiKey="your-tinymce-api-key"
                                value={formData.content}
                                onEditorChange={handleEditorChange}
                                init={{
                                    height: 500,
                                    menubar: true,
                                    plugins: [
                                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                        'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                                    ],
                                    toolbar: 'undo redo | blocks | ' +
                                        'bold italic forecolor | alignleft aligncenter ' +
                                        'alignright alignjustify | bullist numlist outdent indent | ' +
                                        'removeformat | help',
                                }}
                            />
                            {errors.content && (
                                <Typography color="error" variant="caption">
                                    {errors.content[0]}
                                </Typography>
                            )}
                        </Grid>

                        <Grid item xs={12}>
                            <Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end' }}>
                                <Button
                                    variant="outlined"
                                    onClick={() => router.visit('/campaigns')}
                                >
                                    Cancel
                                </Button>
                                <Button
                                    type="submit"
                                    variant="contained"
                                    color="primary"
                                >
                                    {campaign?.id ? 'Update Campaign' : 'Create Campaign'}
                                </Button>
                            </Box>
                        </Grid>
                    </Grid>
                </form>
            </Paper>
        </Box>
    );
};

export default CampaignForm; 