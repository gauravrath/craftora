export function ErrorState({ message = 'Something went wrong.' }: { message?: string }) {
  return (
    <div className="rounded-xl border border-red-500/40 bg-red-500/10 p-6 text-red-200">
      <p className="font-medium">Unable to load data</p>
      <p className="mt-2 text-sm text-red-300">{message}</p>
    </div>
  );
}
