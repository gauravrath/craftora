'use client';

import Image from 'next/image';
import { useState } from 'react';
import { youtubeVideoUrl } from '@/lib/home-content';

type Video = {
  id: string;
  title: string;
  description: string;
  thumbnail: string;
  search: string;
};

export function VideoCard({ video, showWatchButton = false, titleAsHeading = true }: { video: Video; showWatchButton?: boolean; titleAsHeading?: boolean }) {
  const [playing, setPlaying] = useState(false);

  return (
    <article className="overflow-hidden rounded-lg border border-white/15 bg-white/5">
      <div className="relative aspect-video bg-black">
        {playing && video.id ? (
          <iframe
            className="absolute inset-0 size-full"
            src={`https://www.youtube-nocookie.com/embed/${video.id}?autoplay=1&rel=0`}
            title={video.title}
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerPolicy="strict-origin-when-cross-origin"
            allowFullScreen
          />
        ) : (
          <>
            <Image src={video.thumbnail} alt="" fill sizes="(max-width: 768px) 90vw, (max-width: 1200px) 45vw, 25vw" className="object-cover" />
            <a
              href={youtubeVideoUrl(video)}
              target="_blank"
              rel="noreferrer"
              onClick={video.id ? (event) => { event.preventDefault(); setPlaying(true); } : undefined}
              aria-label={`${video.id ? 'Play' : 'Find'} ${video.title} on YouTube`}
              className="absolute inset-0 flex items-center justify-center bg-black/10 transition hover:bg-black/25"
            >
              <span className="inline-flex size-14 items-center justify-center rounded-full bg-[#fffaf2] text-[#a64934] shadow-lg">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" className="ml-1 size-6"><path d="M8 5.8c0-.8.9-1.3 1.6-.9l9.1 5.3a1 1 0 0 1 0 1.7l-9.1 5.3c-.7.4-1.6-.1-1.6-.9V5.8Z" /></svg>
              </span>
            </a>
          </>
        )}
      </div>
      <div className="p-4">
        {titleAsHeading ? <h3 className="font-semibold text-white">{video.title}</h3> : <p className="font-semibold text-white">{video.title}</p>}
        <p className="mt-1 text-sm leading-6 text-[#d3c9bd]">{video.description}</p>
        {!video.id ? <p className="mt-3 text-xs text-[#d3b17a]">Video link to be added</p> : null}
        {showWatchButton && !playing ? (
          video.id ? (
            <button type="button" onClick={() => setPlaying(true)} className="mt-4 inline-flex min-h-10 items-center rounded-md bg-[#d9694f] px-4 text-sm font-semibold text-white hover:bg-[#bd543d]">
              Watch Video
            </button>
          ) : (
            <a href={youtubeVideoUrl(video)} target="_blank" rel="noreferrer" className="mt-4 inline-flex min-h-10 items-center rounded-md bg-[#d9694f] px-4 text-sm font-semibold text-white hover:bg-[#bd543d]">
              Watch Video<span className="sr-only"> (opens YouTube in a new tab)</span><span aria-hidden="true" className="ml-2">↗</span>
            </a>
          )
        ) : null}
      </div>
    </article>
  );
}