export const metadata = {
  title: 'About',
  description: 'Learn how Craftora supports families with trusted, premium protection essentials.',
};

export default function AboutPage() {
  return (
    <main className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
      <p className="text-xs font-medium uppercase tracking-[0.24em] text-emerald-300">About Craftora</p>
      <h1 className="mt-4 text-4xl font-bold text-white">Built for everyday confidence</h1>
      <div className="mt-8 space-y-6 text-lg leading-8 text-slate-300">
        <p>
          Craftora brings together premium protection essentials and practical wellness products for families and homes that want calmer, more prepared routines.
        </p>
        <p>
          We focus on high-quality design, transparent information, and a trustworthy buying experience without making medical claims or promises that exceed the facts.
        </p>
      </div>
    </main>
  );
}
