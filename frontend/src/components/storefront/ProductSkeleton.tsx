export function ProductSkeleton() {
  return (
    <div className="animate-pulse overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/70">
      <div className="h-56 bg-slate-800" />
      <div className="space-y-4 p-5">
        <div className="h-4 w-20 rounded bg-slate-800" />
        <div className="h-6 w-3/4 rounded bg-slate-800" />
        <div className="h-4 w-full rounded bg-slate-800" />
        <div className="h-4 w-5/6 rounded bg-slate-800" />
        <div className="h-8 w-24 rounded bg-slate-800" />
      </div>
    </div>
  );
}
