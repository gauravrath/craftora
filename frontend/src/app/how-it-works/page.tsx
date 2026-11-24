export const metadata = {
  title: 'How it works',
  description: 'A simple overview of how Craftora helps families shop for protection and wellness essentials.',
};

export default function HowItWorksPage() {
  return (
    <main className="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
      <p className="text-xs font-medium uppercase tracking-[0.24em] text-emerald-300">How it works</p>
      <h1 className="mt-4 text-4xl font-bold text-white">A simple path to better protection</h1>
      <div className="mt-10 grid gap-6 md:grid-cols-3">
        {[
          ['1. Browse', 'Explore carefully selected essentials for everyday routines and home protection.'],
          ['2. Choose', 'Compare products, variants, and pricing to find the right fit for your household.'],
          ['3. Shop confidently', 'Place your order through a transparent checkout experience backed by clear policies.'],
        ].map(([step, text]) => (
          <div key={step} className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <p className="text-sm font-medium uppercase tracking-[0.18em] text-emerald-300">{step}</p>
            <p className="mt-4 text-slate-300">{text}</p>
          </div>
        ))}
      </div>
    </main>
  );
}
