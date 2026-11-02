import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  async redirects() {
    return [
      { source: '/partnerships', destination: '/partnership', permanent: true },
      { source: '/bpu-harnessing-talent-report-2026', destination: '/reports/harnessing-talent-2026', permanent: true },
      { source: '/bpu-harnessing-talent-report-2026/', destination: '/reports/harnessing-talent-2026', permanent: true },
    ];
  },
  images: {
    remotePatterns: [
      { protocol: 'https', hostname: 'blackprofessionals.uk' },
      { protocol: 'https', hostname: '*.blackprofessionals.uk' },
      { protocol: 'https', hostname: 'www.gravatar.com' },
      { protocol: 'https', hostname: 'secure.gravatar.com' },
      { protocol: 'https', hostname: 'web.blackprofessionals.uk' },
    ],
    formats: ['image/avif', 'image/webp'],
  },
};

export default nextConfig;
