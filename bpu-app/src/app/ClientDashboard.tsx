'use client';

import React, { useState } from 'react';
import { BPUUser, ACFProfile } from '@/lib/auth';
import { JobListing, CourseItem, CVReview, BPUApi } from '@/lib/api';

interface ClientDashboardProps {
    user: BPUUser;
    initialJobs: JobListing[];
    initialCourses: CourseItem[];
    initialReviews: CVReview[];
    wpCookieHeader: string;
    wpBackendUrl: string;
}

export default function ClientDashboard({
    user,
    initialJobs,
    initialCourses,
    initialReviews,
    wpCookieHeader,
    wpBackendUrl
}: ClientDashboardProps) {
    const [profile, setProfile] = useState<ACFProfile>(user.profile);
    const [cvUrl, setCvUrl] = useState<string>(user.cv_url || '');
    const [jobs] = useState<JobListing[]>(initialJobs);
    const [courses, setCourses] = useState<CourseItem[]>(initialCourses);
    const [reviews] = useState<CVReview[]>(initialReviews);

    // CV Upload State
    const [uploading, setUploading] = useState(false);
    const [uploadSuccess, setUploadSuccess] = useState<string | null>(null);
    const [uploadError, setUploadError] = useState<string | null>(null);

    // Accordion Toggle for CV reviews
    const [activeReviewId, setActiveReviewId] = useState<number | null>(null);

    /**
     * Handle CV upload multipart to Next serverless proxy API
     */
    const handleCVUpload = async (event: React.ChangeEvent<HTMLInputElement>) => {
        const fileList = event.target.files;
        if (!fileList || fileList.length === 0) return;

        const file = fileList[0];
        if (file.type !== 'application/pdf') {
            setUploadError('Only PDF files are supported for automated CV parsing.');
            return;
        }

        setUploading(true);
        setUploadSuccess(null);
        setUploadError(null);

        const formData = new FormData();
        formData.append('cv_file', file);

        try {
            const response = await fetch('/api/member/cv-upload', {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Failed to parse resume.');
            }

            // Successfully parsed CV and filled out profile
            setCvUrl(data.cv_url);
            setProfile(data.parsed_data);
            setUploadSuccess('Your CV was successfully uploaded! Vertex AI (Gemini Pro) has parsed your resume and automatically updated your BPU profile fields below.');
        } catch (error: any) {
            console.error(error);
            setUploadError(error.message || 'Error occurred while connecting to Vertex AI.');
        } finally {
            setUploading(false);
        }
    };

    /**
     * Track Course Progress and redirect to provider
     */
    const handleCourseRedirect = async (course: CourseItem) => {
        // Optimistically set to "In Progress"
        setCourses(prev => prev.map(c => c.id === course.id ? { ...c, status: 'In Progress' } : c));
        
        // Asynchronously track progress in Tutor LMS
        BPUApi.trackCourseProgress(course.id, wpCookieHeader).catch(err => {
            console.error('Course Progress Error:', err);
        });

        // Open provider website
        window.open(course.learn_more_url, '_blank', 'noopener,noreferrer');
    };

    return (
        <div className="flex-1 flex flex-col min-h-screen">
            {/* 1. Header Navigation */}
            <header className="sticky top-0 z-40 bg-card-bg border-b border-card-border backdrop-blur-md bg-opacity-80 transition-all duration-300">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <span className="font-extrabold text-xl tracking-tight"><span className="text-amber-500">BPU</span> App</span>
                        <span className="text-xs px-2 py-0.5 rounded bg-amber-500/10 text-amber-500 font-medium hidden sm:inline">Member Portal</span>
                    </div>

                    <div className="flex items-center gap-4">
                        {/* Standalone Mentorship Portal link */}
                        <a 
                            href="https://pairedbybpu.uk" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            className="text-xs sm:text-sm font-semibold text-amber-500 hover:underline flex items-center gap-1"
                        >
                            🤝 Visit PAIRED Mentorship
                        </a>
                        <div className="h-4 w-px bg-card-border" />
                        <span className="text-sm font-medium hidden md:inline">Hello, {user.display_name}</span>
                        <a 
                            href={`${wpBackendUrl}/wp-login.php?action=logout`}
                            className="text-xs button-secondary py-1.5 px-3"
                        >
                            Sign Out
                        </a>
                    </div>
                </div>
            </header>

            {/* 2. Main Content Grid */}
            <main className="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">
                
                {/* Intro Summary card */}
                <div className="premium-card p-6 border-amber-500/20 bg-amber-500/5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div className="space-y-2">
                        <h2 className="text-2xl font-bold tracking-tight">Welcome to your BPU Dashboard, {profile.first_name || user.display_name}!</h2>
                        <p className="text-sm text-text-muted">
                            {profile.industryfield_of_expertise ? `Expert in ${profile.industryfield_of_expertise}` : 'Complete your profile to unlock tailored recommendations'}
                            {profile.years_of_experience ? ` with ${profile.years_of_experience} years of experience.` : ''}
                        </p>
                    </div>
                    <div className="flex items-center gap-3">
                        <a 
                            href={`${wpBackendUrl}/wp-admin/profile.php`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="button-secondary text-sm"
                        >
                            Edit WordPress Profile
                        </a>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {/* LEFT COLUMN: Profile Data & CV Clinic */}
                    <div className="lg:col-span-2 space-y-8">
                        
                        {/* A. CV Clinic Uploader */}
                        <section className="premium-card p-6 space-y-6">
                            <div>
                                <h3 className="text-lg font-bold">📄 AI CV Clinic</h3>
                                <p className="text-xs text-text-muted">Upload your CV to automatically fill your profile fields using Vertex AI (Gemini Pro).</p>
                            </div>

                            {/* CV Attachment details */}
                            {cvUrl && (
                                <div className="p-3 rounded-lg border border-emerald-500/20 bg-emerald-500/5 text-xs flex items-center justify-between">
                                    <span className="truncate">Attached Resume: <a href={cvUrl} target="_blank" rel="noopener noreferrer" className="text-emerald-500 underline font-semibold">Download CV File</a></span>
                                    <span className="text-emerald-500 font-medium">✓ Active</span>
                                </div>
                            )}

                            {/* Drag Drop / Selector Area */}
                            <div className="border-2 border-dashed border-card-border hover:border-amber-500/40 rounded-xl p-8 text-center transition-colors duration-200">
                                <input 
                                    type="file" 
                                    id="cv-upload-input" 
                                    className="hidden" 
                                    accept=".pdf"
                                    onChange={handleCVUpload}
                                    disabled={uploading}
                                />
                                <label htmlFor="cv-upload-input" className="cursor-pointer space-y-2 block">
                                    <div className="text-3xl">📥</div>
                                    <div className="text-sm font-semibold">
                                        {uploading ? 'Processing CV with Vertex AI...' : 'Select your PDF Resume'}
                                    </div>
                                    <p className="text-xs text-text-muted">
                                        Click to browse files. Supports PDF format only.
                                    </p>
                                </label>
                            </div>

                            {uploading && (
                                <div className="space-y-2">
                                    <div className="h-1.5 w-full bg-card-border rounded-full overflow-hidden">
                                        <div className="h-full bg-amber-500 rounded-full animate-pulse w-3/4" />
                                    </div>
                                    <div className="text-xxs text-center text-text-muted">Gemini Pro is extracting skills and experience structures...</div>
                                </div>
                            )}

                            {uploadSuccess && <div className="p-3 rounded bg-emerald-500/10 text-emerald-500 text-xs">{uploadSuccess}</div>}
                            {uploadError && <div className="p-3 rounded bg-red-500/10 text-red-500 text-xs">{uploadError}</div>}
                        </section>

                        {/* B. Professional Profile Fields */}
                        <section className="premium-card p-6 space-y-6">
                            <div>
                                <h3 className="text-lg font-bold">👤 Profile Information</h3>
                                <p className="text-xs text-text-muted">These details are stored securely as custom fields (ACF) on your BPU account.</p>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div className="border border-card-border p-3 rounded-lg bg-background">
                                    <div className="text-xxs text-text-muted uppercase font-bold">First Name</div>
                                    <div className="font-semibold">{profile.first_name || 'Not filled'}</div>
                                </div>
                                <div className="border border-card-border p-3 rounded-lg bg-background">
                                    <div className="text-xxs text-text-muted uppercase font-bold">Last Name</div>
                                    <div className="font-semibold">{profile.last_name || 'Not filled'}</div>
                                </div>
                                <div className="border border-card-border p-3 rounded-lg bg-background">
                                    <div className="text-xxs text-text-muted uppercase font-bold">Country</div>
                                    <div className="font-semibold">{profile.country_location || 'Not filled'}</div>
                                </div>
                                <div className="border border-card-border p-3 rounded-lg bg-background">
                                    <div className="text-xxs text-text-muted uppercase font-bold">City / Location</div>
                                    <div className="font-semibold">{profile.location_city || 'Not filled'}</div>
                                </div>
                                <div className="border border-card-border p-3 rounded-lg bg-background">
                                    <div className="text-xxs text-text-muted uppercase font-bold">Employment Status</div>
                                    <div className="font-semibold">{profile.current_employment_status || 'Not filled'}</div>
                                </div>
                                <div className="border border-card-border p-3 rounded-lg bg-background">
                                    <div className="text-xxs text-text-muted uppercase font-bold">Field of Expertise</div>
                                    <div className="font-semibold">{profile.industryfield_of_expertise || 'Not filled'}</div>
                                </div>
                                <div className="border border-card-border p-3 rounded-lg bg-background md:col-span-2">
                                    <div className="text-xxs text-text-muted uppercase font-bold">Skills</div>
                                    <div className="font-semibold">{profile.skills_separate || 'No skills listed'}</div>
                                </div>
                                <div className="border border-card-border p-3 rounded-lg bg-background md:col-span-2">
                                    <div className="text-xxs text-text-muted uppercase font-bold">Professional Biography</div>
                                    <div className="font-semibold whitespace-pre-line text-xs">{profile.user_bio || 'No bio written yet. Upload a CV to generate.'}</div>
                                </div>
                            </div>
                        </section>

                        {/* C. Manual CV Clinic Critique Reviews */}
                        <section className="premium-card p-6 space-y-6">
                            <div>
                                <div className="flex items-center justify-between">
                                    <h3 className="text-lg font-bold">📋 BPU CV Clinic Reviews</h3>
                                    <span className="text-xxs font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-500">Pro Feature</span>
                                </div>
                                <p className="text-xs text-text-muted">Manual critique reports written by BPU professional recruiters. Upgrade to the Pro membership tier to request a manual review.</p>
                            </div>

                            {reviews.length === 0 ? (
                                <div className="text-center py-6 border border-dashed border-card-border rounded-lg text-xs text-text-muted">
                                    No manual reviews found. Upload your resume and upgrade to the Pro Tier to request a professional CV review.
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {reviews.map((review) => (
                                        <div key={review.id} className="border border-card-border rounded-lg overflow-hidden">
                                            <button 
                                                onClick={() => setActiveReviewId(activeReviewId === review.id ? null : review.id)}
                                                className="w-full px-4 py-3 flex items-center justify-between bg-hover-bg/30 text-left font-semibold text-sm"
                                            >
                                                <div className="flex items-center gap-3">
                                                    <span>{review.title}</span>
                                                    {review.score && (
                                                        <span className="text-xxs px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-500">
                                                            Score: {review.score}/100
                                                        </span>
                                                    )}
                                                </div>
                                                <span>{activeReviewId === review.id ? '▲' : '▼'}</span>
                                            </button>
                                            
                                            {activeReviewId === review.id && (
                                                <div className="p-4 border-t border-card-border bg-card-bg text-xs space-y-4">
                                                    <div className="whitespace-pre-line leading-relaxed">{review.critique}</div>
                                                    <div className="flex items-center justify-between text-xxs text-text-muted pt-2 border-t border-card-border">
                                                        <span>Reviewer: {review.reviewer}</span>
                                                        <span>Date: {new Date(review.date).toLocaleDateString()}</span>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </section>
                    </div>

                    {/* RIGHT COLUMN: Jobs & Courses */}
                    <div className="space-y-8">
                        
                        {/* A. AI Job Recommendations */}
                        <section className="premium-card p-6 space-y-4">
                            <div>
                                <h3 className="text-md font-bold">💼 Recommended Jobs</h3>
                                <p className="text-xxs text-text-muted">Custom daily recommendations semantic-matched with your profile fields.</p>
                            </div>

                            <div className="space-y-4">
                                {jobs.length === 0 ? (
                                    <div className="text-xs text-text-muted py-4 text-center">No matching job listings found today. Check back tomorrow!</div>
                                ) : (
                                    jobs.map((job) => (
                                        <div key={job.id} className="border border-card-border rounded-lg p-3 space-y-2 hover:bg-hover-bg/25 transition-colors">
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <h4 className="text-xs font-bold truncate max-w-[150px]">{job.title}</h4>
                                                    <p className="text-xxs text-text-muted">{job.company} • {job.location}</p>
                                                </div>
                                                {job.match_score && (
                                                    <span className="text-xxs font-bold px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-500">
                                                        {job.match_score}% Match
                                                    </span>
                                                )}
                                            </div>
                                            <div className="flex justify-between items-center text-xxs pt-1">
                                                <span className="text-text-muted">Posted: {job.date_posted}</span>
                                                {/* Link runs through our Next click-tracker route handler */}
                                                <a 
                                                    href={`/api/jobs/track-click?jobId=${job.id}&url=${encodeURIComponent(job.apply_url)}`}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="font-bold text-amber-500 hover:underline"
                                                >
                                                    Apply Partner Site →
                                                </a>
                                            </div>
                                        </div>
                                    ))
                                )}
                            </div>
                        </section>

                        {/* B. Tutor LMS Click-Through Courses */}
                        <section className="premium-card p-6 space-y-4">
                            <div>
                                <h3 className="text-md font-bold">🎓 Accredited Courses</h3>
                                <p className="text-xxs text-text-muted">Accredited training modules by BPU partners. Links redirect to providers.</p>
                            </div>

                            <div className="space-y-4">
                                {courses.map((course) => (
                                    <div key={course.id} className="border border-card-border rounded-lg p-3 space-y-2 flex flex-col justify-between">
                                        <div className="space-y-1">
                                            <div className="flex justify-between items-center">
                                                <span className="text-xxs uppercase tracking-wider text-text-muted">{course.category}</span>
                                                <span className={`text-xxs font-medium px-2 py-0.5 rounded-full ${
                                                    course.status === 'In Progress' ? 'bg-amber-500/10 text-amber-500' : 'bg-card-border text-text-muted'
                                                }`}>
                                                    {course.status}
                                                </span>
                                            </div>
                                            <h4 className="text-xs font-bold leading-snug">{course.title}</h4>
                                            <p className="text-xxs text-text-muted">Provider: {course.provider}</p>
                                        </div>
                                        
                                        {/* Logs start trigger in Tutor LMS and opens link */}
                                        <button 
                                            onClick={() => handleCourseRedirect(course)}
                                            className="w-full text-center text-xxs button-secondary py-1 px-2 font-bold justify-center"
                                        >
                                            Learn More & Redirect →
                                        </button>
                                    </div>
                                ))}
                            </div>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    );
}
