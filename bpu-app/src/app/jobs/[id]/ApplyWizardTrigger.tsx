'use client';

import { useState } from 'react';
import { Job } from '../types';
import { BPUUser } from '@/lib/auth';
import ApplyWizard from './ApplyWizard';

interface Props {
    job: Job;
    user: BPUUser;
}

export default function ApplyWizardTrigger({ job, user }: Props) {
    const [open, setOpen] = useState(false);

    return (
        <>
            <button
                type="button"
                className="btn btn-amber w-full justify-center btn-lg"
                onClick={() => setOpen(true)}
            >
                Apply for this role
            </button>

            {open && (
                <ApplyWizard
                    job={job}
                    user={user}
                    onClose={() => setOpen(false)}
                />
            )}
        </>
    );
}
