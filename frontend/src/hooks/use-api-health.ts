'use client';

import { useEffect, useState } from 'react';
import { api } from '@/lib/api';
import type { ApiStatus } from '@/types/api';

export function useApiHealth() {
  const [status, setStatus] = useState<ApiStatus | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function load() {
      try {
        const response = await api.get<ApiStatus>('/health');
        setStatus(response.data ?? null);
        setError(null);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Unknown error');
      } finally {
        setLoading(false);
      }
    }

    load();
  }, []);

  return { status, loading, error };
}
