import { LinkedinLogo, TwitterLogo, InstagramLogo } from '@phosphor-icons/react/dist/ssr';

export default function SiteFooter() {
    return (
        <footer className="bg-hp-dark text-white pt-20 pb-10 border-t-[8px] border-hp-accent">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

                    {/* Brand */}
                    <div className="lg:col-span-1">
                        <div className="mb-6">
                            <div className="inline-block bg-white rounded-xl px-3 py-2">
                                {/* eslint-disable-next-line @next/next/no-img-element */}
                                <img src="/images/bpu-logo.png" alt="Black Professionals UK" className="h-10 w-auto" />
                            </div>
                        </div>
                        <p className="text-gray-400 font-medium text-sm mb-6">
                            Equipping professionals. Meeting inclusion ambitions. Dismantling barriers.
                        </p>
                        <div className="flex gap-4">
                            <a href="https://linkedin.com/company/blackprofessionalsuk" target="_blank" rel="noreferrer" className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-hp-accent transition-colors">
                                <LinkedinLogo weight="fill" size={20} />
                            </a>
                            <a href="https://twitter.com/blackprosuk" target="_blank" rel="noreferrer" className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-hp-accent transition-colors">
                                <TwitterLogo weight="fill" size={20} />
                            </a>
                            <a href="https://instagram.com/blackprofessionalsuk" target="_blank" rel="noreferrer" className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-hp-accent transition-colors">
                                <InstagramLogo weight="fill" size={20} />
                            </a>
                        </div>
                    </div>

                    {/* Platform */}
                    <div>
                        <h4 className="font-black uppercase tracking-wider mb-6 text-hp-accent">Platform</h4>
                        <ul className="space-y-3 font-bold text-sm text-gray-300">
                            {[
                                ['Work Placements', '/membership/work-placement'],
                                ['Job Board', '/jobs'],
                                ['CV Clinic & Interview Prep', '/dashboard/cv-clinic'],
                                ['Free Courses', '/courses'],
                                ['Mentorship Programme', 'https://pairedbybpu.uk'],
                                ['Membership Support', '/support'],
                            ].map(([label, href]) => (
                                <li key={label}><a href={href} className="hover:text-white transition-colors">{label}</a></li>
                            ))}
                        </ul>
                    </div>

                    {/* Network */}
                    <div>
                        <h4 className="font-black uppercase tracking-wider mb-6 text-hp-accent">Network</h4>
                        <ul className="space-y-3 font-bold text-sm text-gray-300">
                            {[
                                ['Our Partners', '/partnership'],
                                ['Become a Partner', '/partnership#enquiry'],
                                ['Post a Job', '/employer/jobs'],
                                ['Community & Networking', '/community'],
                                ['Stories & Articles', '/stories'],
                            ].map(([label, href]) => (
                                <li key={label}><a href={href} className="hover:text-white transition-colors">{label}</a></li>
                            ))}
                        </ul>
                    </div>

                    {/* Company */}
                    <div>
                        <h4 className="font-black uppercase tracking-wider mb-6 text-hp-accent">Company</h4>
                        <ul className="space-y-3 font-bold text-sm text-gray-300">
                            {[
                                ['About Us', '/about'],
                                ['Contact Us', '/contact'],
                                ['In The Press', '/press'],
                                ['Resources', '/resources'],
                            ].map(([label, href]) => (
                                <li key={label}><a href={href} className="hover:text-white transition-colors">{label}</a></li>
                            ))}
                        </ul>
                    </div>
                </div>

                {/* Bottom bar */}
                <div className="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-bold text-gray-500 uppercase tracking-wide">
                    <p>&copy; {new Date().getFullYear()} Black Professionals United Kingdom. All rights reserved.</p>
                    <div className="flex gap-6">
                        <a href="/terms"   className="hover:text-white transition-colors">Usage Terms</a>
                        <a href="/cookies" className="hover:text-white transition-colors">Cookie Policy</a>
                        <a href="/privacy" className="hover:text-white transition-colors">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </footer>
    );
}
