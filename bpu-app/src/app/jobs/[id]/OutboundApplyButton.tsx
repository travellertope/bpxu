/**
 * Outbound apply link — uses the server-side /go/[id] redirect.
 * Tracking fires on the server so it works with right-click, ad blockers,
 * keyboard navigation, and without any JavaScript.
 */
export default function OutboundApplyButton({ jobId }: { jobId: number }) {
    return (
        <a
            href={`/go/${jobId}`}
            target="_blank"
            rel="noopener noreferrer"
            className="btn btn-amber w-full text-center"
        >
            Apply at partner site →
        </a>
    );
}
