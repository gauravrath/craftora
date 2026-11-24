import type { Metadata } from 'next';
import Link from 'next/link';
import { IdeaCard } from '@/components/storefront/IdeaCard';
import { getProducts } from '@/lib/catalog';
import { diyIdeas, ideaTypes } from '@/lib/diy-ideas';
import { youtubeChannelUrl } from '@/lib/home-content';
import type { Product } from '@/types/catalog';

export const metadata: Metadata = {
  title: 'DIY Ideas',
  description: 'Find your next creative idea: festival crafts, home decor DIY, handmade gifts, and beginner projects, with the materials to make them.',
};

type SearchParams = Record<string, string | string[] | undefined>;

function getParam(params: SearchParams, key: string) {
  const value = params[key];
  return typeof value === 'string' ? value : '';
}

export default async function DiyIdeasPage({ searchParams }: { searchParams: Promise<SearchParams> }) {
  const params = await searchParams;
  const query = getParam(params, 'q').trim();
  const type = getParam(params, 'type');
  const difficulty = getParam(params, 'difficulty');
  const occasion = getParam(params, 'occasion');

  let products: Product[] = [];
  try {
    products = (await getProducts({ per_page: 100, sort_by: 'name', sort_dir: 'asc' })).data ?? [];
  } catch {
    products = [];
  }

  const difficulties = [...new Set(diyIdeas.map((idea) => idea.difficulty).filter(Boolean))] as string[];
  const occasions = [...new Set(diyIdeas.map((idea) => idea.occasion).filter(Boolean))] as string[];

  const matches = diyIdeas.filter((idea) => {
    const text = `${idea.title} ${idea.description} ${idea.type} ${idea.occasion ?? ''}`.toLowerCase();
    return (!query || text.includes(query.toLowerCase()))
      && (!type || idea.type === type)
      && (!difficulty || idea.difficulty === difficulty)
      && (!occasion || idea.occasion === occasion);
  });

  const sections = ideaTypes
    .map((name) => ({ name, ideas: matches.filter((idea) => idea.type === name) }))
    .filter((section) => section.ideas.length > 0);

  const hasFilters = Boolean(query || type || difficulty || occasion);
  const fieldClass = 'mt-1 w-full rounded-md border border-[#e2d8cc] bg-white px-3 py-2.5 text-sm text-[#43392f] outline-none focus:border-[#a64934] focus:ring-2 focus:ring-[#a64934]/15';

  return (
    <main className="min-h-screen bg-[#fbf8f3] text-[#332d28]">
      <section className="bg-[#f2e9dc]">
        <div className="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-12 lg:py-12">
          <nav aria-label="Breadcrumb" className="mb-6 text-sm text-[#776b5e]">
            <ol className="flex items-center gap-2">
              <li><Link href="/" className="hover:text-[#a64934]">Home</Link></li>
              <li aria-hidden="true">/</li>
              <li aria-current="page" className="font-medium text-[#43392f]">DIY Ideas</li>
            </ol>
          </nav>
          <p className="text-xs font-semibold uppercase text-[#a64934]">Inspiration for makers</p>
          <h1 className="mt-3 max-w-2xl font-serif text-4xl leading-tight text-[#332d28] sm:text-5xl">Find Your Next Creative Idea</h1>
          <p className="mt-4 max-w-xl text-base leading-7 text-[#665b50]">Explore projects you can make at home, watch the tutorial, and pick up the materials to get started.</p>
          <a href={youtubeChannelUrl} target="_blank" rel="noreferrer" className="mt-6 inline-flex min-h-11 items-center rounded-md bg-[#a64934] px-5 text-sm font-semibold text-white hover:bg-[#873b2a]">Watch on YouTube <span aria-hidden="true" className="ml-2">↗</span></a>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-5 py-10 sm:px-8 lg:px-12 lg:py-14">
        <form method="get" action="/diy-ideas" role="search" aria-label="Search DIY ideas" className="grid gap-3 rounded-md border border-[#e9dfd3] bg-white p-4 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1fr_1fr_auto] lg:items-end">
          <label htmlFor="idea-search" className="text-sm font-medium text-[#51473e]">
            Search ideas
            <input id="idea-search" name="q" type="search" defaultValue={query} placeholder="Toran, gifts, beginner..." className={fieldClass} />
          </label>
          <label htmlFor="idea-type" className="text-sm font-medium text-[#51473e]">
            Project type
            <select id="idea-type" name="type" defaultValue={type} className={fieldClass}>
              <option value="">All types</option>
              {ideaTypes.map((name) => <option key={name} value={name}>{name}</option>)}
            </select>
          </label>
          {difficulties.length > 0 ? (
            <label htmlFor="idea-difficulty" className="text-sm font-medium text-[#51473e]">
              Difficulty
              <select id="idea-difficulty" name="difficulty" defaultValue={difficulty} className={fieldClass}>
                <option value="">Any</option>
                {difficulties.map((name) => <option key={name} value={name}>{name}</option>)}
              </select>
            </label>
          ) : null}
          {occasions.length > 0 ? (
            <label htmlFor="idea-occasion" className="text-sm font-medium text-[#51473e]">
              Occasion
              <select id="idea-occasion" name="occasion" defaultValue={occasion} className={fieldClass}>
                <option value="">Any</option>
                {occasions.map((name) => <option key={name} value={name}>{name}</option>)}
              </select>
            </label>
          ) : null}
          <div className="flex gap-2">
            <button type="submit" className="min-h-11 rounded-md bg-[#a64934] px-5 text-sm font-semibold text-white hover:bg-[#873b2a]">Apply</button>
            {hasFilters ? <Link href="/diy-ideas" className="inline-flex min-h-11 items-center rounded-md border border-[#d9cbbd] px-4 text-sm font-semibold text-[#51473e] hover:bg-[#f2e9dc]">Clear</Link> : null}
          </div>
        </form>

        <p aria-live="polite" className="mt-4 text-sm text-[#776b5e]">{matches.length} {matches.length === 1 ? 'idea' : 'ideas'}</p>

        {sections.length === 0 ? (
          <div className="mt-6 rounded-md border border-dashed border-[#d8cabb] bg-white px-6 py-14 text-center">
            <h2 className="font-serif text-2xl text-[#43392f]">No ideas found</h2>
            <p className="mt-2 text-sm text-[#776b5e]">Try a different search or clear your filters.</p>
            <Link href="/diy-ideas" className="mt-5 inline-flex min-h-10 items-center rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white hover:bg-[#873b2a]">Show all ideas</Link>
          </div>
        ) : (
          sections.map((section) => (
            <section key={section.name} className="mt-10" aria-labelledby={`section-${section.name.replace(/\W+/g, '-').toLowerCase()}`}>
              <h2 id={`section-${section.name.replace(/\W+/g, '-').toLowerCase()}`} className="mb-4 font-serif text-2xl text-[#332d28]">{section.name}</h2>
              <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                {section.ideas.map((idea) => <IdeaCard key={idea.slug} idea={idea} products={products} />)}
              </div>
            </section>
          ))
        )}
      </div>
    </main>
  );
}
