'use client';

interface OutboundApplyButtonProps {
    jobId: number;
    applyUrl: string;
}

export default function OutboundApplyButton({ jobId, applyUrl }: OutboundApplyButtonProps) {
    async function handleClick() {
        // Fire-and-forget click tracking
        fetch(`/api/jobs/${jobId}/click`, { method: 'POST' }).catch(() => {});
        window.open(applyUrl, '_blank', 'noopener,noreferrer');
    }

    return (
        <button
            type="button"
            onClick={handleClick}
            className="btn btn-amber w-full justify-center"
        >
            Apply at partner site →
        </button>
    );
}
