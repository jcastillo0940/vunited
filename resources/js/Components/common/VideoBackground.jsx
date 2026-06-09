export function extractYouTubeId(url) {
    if (!url) return null;
    const patterns = [
        /[?&]v=([^&#]+)/,
        /youtu\.be\/([^?#]+)/,
        /youtube\.com\/embed\/([^?#]+)/,
        /youtube\.com\/shorts\/([^?#]+)/,
    ];
    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match) return match[1];
    }
    return null;
}

export default function VideoBackground({ videoId, gradient }) {
    if (!videoId) return null;
    return (
        <>
            <div className="absolute inset-0 z-0 overflow-hidden">
                <iframe
                    src={`https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&loop=1&controls=0&rel=0&showinfo=0&playlist=${videoId}&playsinline=1&modestbranding=1&disablekb=1&iv_load_policy=3&fs=0`}
                    title="Hero video"
                    allow="autoplay; loop"
                    style={{
                        position: 'absolute',
                        top: '50%',
                        left: '50%',
                        transform: 'translate(-50%, -50%)',
                        width: '177.78vh',
                        minWidth: '100%',
                        height: '56.25vw',
                        minHeight: '100%',
                        border: 'none',
                        pointerEvents: 'none',
                    }}
                />
            </div>
            <div className="absolute inset-0 z-[1]" style={{ background: gradient }} />
        </>
    );
}
