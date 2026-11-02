import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';

export const metadata = {
    title: 'Cookie Policy | Black Professionals United Kingdom',
    description: 'How Black Professionals United Kingdom uses cookies and similar tracking technologies on our website.',
};

const LAST_UPDATED = 'November 01, 2026';

export default function CookiePolicyPage() {
    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
                {/* Hero */}
                <section className="py-16 bg-hp-dark text-white">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-5">
                            Legal
                        </div>
                        <h1 className="text-4xl sm:text-5xl font-black uppercase tracking-tight leading-none mb-4">
                            Cookie Policy
                        </h1>
                        <p className="text-white/60 text-sm">Last updated {LAST_UPDATED}</p>
                    </div>
                </section>

                {/* Body */}
                <section className="py-14">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="prose-legal">

                            <p>
                                This Cookie Policy explains how <strong>Black Professionals United Kingdom</strong> ("we", "us", and "our") uses cookies and similar technologies to recognise you when you visit our website at <a href="https://blackprofessionals.uk">blackprofessionals.uk</a>. It explains what these technologies are and why we use them, as well as your rights to control our use of them.
                            </p>
                            <p>
                                In some cases we may use cookies to collect personal information, or that becomes personal information if we combine it with other information.
                            </p>

                            <h2>What Are Cookies?</h2>
                            <p>
                                Cookies are small data files that are placed on your computer or mobile device when you visit a website. Cookies are widely used by website owners in order to make their websites work, or to work more efficiently, as well as to provide reporting information.
                            </p>
                            <p>
                                Cookies set by the website owner (in this case, Black Professionals United Kingdom) are called "first-party cookies". Cookies set by parties other than the website owner are called "third-party cookies". Third-party cookies enable third-party features or functionality to be provided on or through the website (e.g. advertising, interactive content, and analytics). The parties that set these third-party cookies can recognise your computer both when it visits our website and also when it visits certain other websites.
                            </p>

                            <h2>Why Do We Use Cookies?</h2>
                            <p>
                                We use first- and third-party cookies for several reasons. Some cookies are required for technical reasons in order for our website to operate — we refer to these as "essential" or "strictly necessary" cookies. Other cookies enable us to track and target the interests of our users to enhance the experience on our website. Third parties serve cookies through our website for advertising, analytics, and other purposes, as described in more detail below.
                            </p>

                            <h2>How Can I Control Cookies?</h2>
                            <p>
                                You have the right to decide whether to accept or reject cookies. You can exercise your cookie rights by setting your preferences in the Cookie Consent Manager, which can be found in the notification banner and on our website. If you choose to reject cookies, you may still use our website though your access to some functionality and areas of our website may be restricted. You may also set or amend your web browser controls to accept or refuse cookies. As the means by which you can refuse cookies through your web browser controls vary from browser to browser, you should visit your browser's help menu for more information.
                            </p>
                            <p>
                                In addition, most advertising networks offer you a way to opt out of targeted advertising. For more information, please visit <a href="http://www.aboutads.info/choices/" target="_blank" rel="noreferrer">www.aboutads.info/choices</a> or <a href="http://www.youronlinechoices.com" target="_blank" rel="noreferrer">www.youronlinechoices.com</a>.
                            </p>

                            <h2>Types of Cookies We Use</h2>

                            <h3>Essential Website Cookies</h3>
                            <p>These cookies are strictly necessary to provide you with services available through our website and to use some of its features, such as access to secure areas.</p>
                            <div className="cookie-table">
                                <table>
                                    <thead>
                                        <tr><th>Name</th><th>Purpose</th><th>Provider</th><th>Expires</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>bpu_session</code></td>
                                            <td>Stores your authenticated session token to keep you logged in to the BPU member portal.</td>
                                            <td>blackprofessionals.uk</td>
                                            <td>Session / 7 days</td>
                                        </tr>
                                        <tr>
                                            <td><code>__tlbcpv</code></td>
                                            <td>Used to record unique visitor views of the consent banner.</td>
                                            <td>termly.io</td>
                                            <td>1 year</td>
                                        </tr>
                                        <tr>
                                            <td><code>wordpress_logged_in_*</code></td>
                                            <td>Used by WordPress to identify authenticated users on the backend.</td>
                                            <td>blackprofessionals.uk</td>
                                            <td>Session</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h3>Analytics and Customisation Cookies</h3>
                            <p>These cookies collect information that is used either in aggregate form to help us understand how our website is being used or how effective our marketing campaigns are, or to help us customise our website for you.</p>
                            <div className="cookie-table">
                                <table>
                                    <thead>
                                        <tr><th>Name</th><th>Purpose</th><th>Provider</th><th>Expires</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>_ga</code></td>
                                            <td>Registers a unique ID that is used to generate statistical data on how the visitor uses the website.</td>
                                            <td>Google Analytics</td>
                                            <td>2 years</td>
                                        </tr>
                                        <tr>
                                            <td><code>_gid</code></td>
                                            <td>Registers a unique ID that is used to generate statistical data on how the visitor uses the website.</td>
                                            <td>Google Analytics</td>
                                            <td>24 hours</td>
                                        </tr>
                                        <tr>
                                            <td><code>gen_204</code></td>
                                            <td>A pixel tracker used to measure page performance and user engagement metrics.</td>
                                            <td>blackprofessionals.uk</td>
                                            <td>Session</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h3>Unclassified Cookies</h3>
                            <p>These are cookies that have not yet been categorised. We are in the process of classifying these cookies with the help of their providers.</p>
                            <div className="cookie-table">
                                <table>
                                    <thead>
                                        <tr><th>Name</th><th>Purpose</th><th>Type</th><th>Expires</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>featured-images_count_files</code></td>
                                            <td>Stores a count of featured images processed for display optimisation.</td>
                                            <td>HTML local storage</td>
                                            <td>Persistent</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h2>What About Other Tracking Technologies, Like Web Beacons?</h2>
                            <p>
                                Cookies are not the only way to recognise or track visitors to a website. We may use other, similar technologies from time to time, like web beacons (sometimes called "tracking pixels" or "clear gifs"). These are tiny graphics files that contain a unique identifier that enable us to recognise when someone has visited our website or opened an email including them. This allows us, for example, to monitor the traffic patterns of users from one page within a website to another, to deliver or communicate with cookies, to understand whether you have come to the website from an online advertisement displayed on a third-party website, to improve site performance, and to measure the success of email marketing campaigns. In many instances, these technologies are reliant on cookies to function properly, and so declining cookies will impair their functioning.
                            </p>

                            <h2>Do You Serve Targeted Advertising?</h2>
                            <p>
                                Third parties may serve cookies on your computer or mobile device to serve advertising through our website. These companies may use information about your visits to this and other websites in order to provide relevant advertisements about goods and services that you may be interested in. They may also employ technology that is used to measure the effectiveness of advertisements. The information collected through this process does not enable us or them to identify your name, contact details, or other details that directly identify you unless you choose to provide these.
                            </p>

                            <h2>How Often Will You Update This Cookie Policy?</h2>
                            <p>
                                We may update this Cookie Policy from time to time in order to reflect, for example, changes to the cookies we use or for other operational, legal, or regulatory reasons. Please therefore re-visit this Cookie Policy regularly to stay informed about our use of cookies and related technologies. The date at the top of this Cookie Policy indicates when it was last updated.
                            </p>

                            <h2>Where Can I Get Further Information?</h2>
                            <p>
                                If you have any questions about our use of cookies or other technologies, please email us at <a href="mailto:info@blackprofessionals.uk">info@blackprofessionals.uk</a> or by post to:
                            </p>
                            <address>
                                Black Professionals United Kingdom<br />
                                5 South Charlotte Street<br />
                                Edinburgh EH2 4AN<br />
                                United Kingdom
                            </address>

                        </div>
                    </div>
                </section>
            </main>
                <SiteFooter />
        </div>
    );
}
