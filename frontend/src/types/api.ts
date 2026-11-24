export type ApiStatus = {
  status: string;
  service: string;
  timestamp: string;
};

export type ApiResponse<T> = {
  success?: boolean;
  message?: string;
  data?: T;
  errors?: Record<string, string[] | string>;
};
