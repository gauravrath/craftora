"use client";

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { getProfile, logoutUser, readAuthSession } from '@/lib/auth';
import type { AuthUser } from '@/types/auth';

export default function ProfilePage() {
  const router = useRouter();
  const [user, setUser] = useState<AuthUser | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const session = readAuthSession();
    if (!session) {
      router.replace('/login');
      return;
    }

    getProfile()
      .then((profile) => setUser(profile))
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'Unable to load profile.');
      })
      .finally(() => setLoading(false));
  }, [router]);

  async function handleLogout() {
    try {
      await logoutUser();
      router.push('/login');
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to log out.');
    }
  }

  if (loading) {
    return (
      <main className="flex min-h-screen items-center justify-center bg-slate-950 text-slate-100">
        <div className="rounded-xl border border-slate-800 bg-slate-900 px-6 py-4 text-slate-300">Loading profile...</div>
      </main>
    );
  }

  if (!user) {
    return (
      <main className="flex min-h-screen items-center justify-center bg-slate-950 text-slate-100">
        <div className="max-w-md rounded-xl border border-rose-500/40 bg-rose-500/10 p-6 text-rose-200">
          {error ?? 'Your profile could not be loaded.'}
        </div>
      </main>
    );
  }

  return (
    <main className="min-h-screen bg-slate-950 px-6 py-12 text-slate-100">
      <div className="mx-auto max-w-3xl rounded-3xl border border-slate-800 bg-slate-900/80 p-8 shadow-2xl shadow-slate-950/40">
        <div className="flex flex-col gap-4 border-b border-slate-800 pb-6 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p className="text-sm uppercase tracking-[0.2em] text-emerald-300">Profile</p>
            <h1 className="mt-2 text-3xl font-bold text-white">{user.name}</h1>
          </div>
          <div className="flex items-center gap-3">
            <Link
              href="/account/orders"
              className="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 font-medium text-emerald-200 hover:bg-emerald-500/20"
            >
              My orders
            </Link>
            <button
              type="button"
              onClick={handleLogout}
              className="rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 font-medium text-rose-200 hover:bg-rose-500/20"
            >
              Log out
            </button>
          </div>
        </div>

        <div className="mt-6 grid gap-4 md:grid-cols-2">
          <div className="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
            <p className="text-sm text-slate-400">Email</p>
            <p className="mt-2 font-medium text-white">{user.email}</p>
          </div>
          <div className="rounded-xl border border-slate-800 bg-slate-950/50 p-4">
            <p className="text-sm text-slate-400">Phone number</p>
            <p className="mt-2 font-medium text-white">{user.phone_number ?? 'Not provided'}</p>
          </div>
          <div className="rounded-xl border border-slate-800 bg-slate-950/50 p-4 md:col-span-2">
            <p className="text-sm text-slate-400">Address</p>
            <p className="mt-2 font-medium text-white">{user.address ?? 'Not provided'}</p>
          </div>
          <div className="rounded-xl border border-slate-800 bg-slate-950/50 p-4 md:col-span-2">
            <p className="text-sm text-slate-400">Verification</p>
            <p className="mt-2 font-medium text-white">
              {user.email_verified_at ? 'Email verified' : 'Email not yet verified'}
            </p>
          </div>
        </div>
      </div>
    </main>
  );
}
