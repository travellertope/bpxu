import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';

export const metadata = {
    title: 'Usage Terms | Black Professionals United Kingdom',
    description: 'The legal terms governing your use of the Black Professionals United Kingdom platform and services.',
};

const LAST_UPDATED = 'November 01, 2026';

export default function UsageTermsPage() {
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
                            Usage Terms
                        </h1>
                        <p className="text-white/60 text-sm">Last updated {LAST_UPDATED}</p>
                    </div>
                </section>

                {/* Body */}
                <section className="py-14">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="prose-legal">

                            <h2>Agreement to Our Legal Terms</h2>
                            <p>
                                We are <strong>Black Professionals United Kingdom</strong> ('Company', 'we', 'us', or 'our'). We operate the website <a href="https://blackprofessionals.uk">https://blackprofessionals.uk</a> (the 'Site'), as well as any other related products and services that refer or link to these legal terms (the 'Legal Terms') (collectively, the 'Services').
                            </p>
                            <p>
                                We are a free platform where UK-based Black professionals are equipped with skills and information to be the best they can within their chosen careers, while organisations' inclusion and diversity ambitions are being met.
                            </p>
                            <p>
                                You can contact us by email at <a href="mailto:info@blackprofessionals.uk">info@blackprofessionals.uk</a> or by mail to 5 South Charlotte Street, Edinburgh EH2 4AN, United Kingdom.
                            </p>
                            <p>
                                These Legal Terms constitute a legally binding agreement made between you, whether personally or on behalf of an entity ('you'), and Black Professionals United Kingdom, concerning your access to and use of the Services. You agree that by accessing the Services, you have read, understood, and agreed to be bound by all of these Legal Terms. <strong>IF YOU DO NOT AGREE WITH ALL OF THESE LEGAL TERMS, THEN YOU ARE EXPRESSLY PROHIBITED FROM USING THE SERVICES AND YOU MUST DISCONTINUE USE IMMEDIATELY.</strong>
                            </p>
                            <p>
                                Supplemental terms and conditions or documents that may be posted on the Services from time to time are hereby expressly incorporated herein by reference. We reserve the right, in our sole discretion, to make changes or modifications to these Legal Terms from time to time. We will alert you about any changes by updating the 'Last updated' date of these Legal Terms, and you waive any right to receive specific notice of each such change. It is your responsibility to periodically review these Legal Terms to stay informed of updates.
                            </p>

                            <h2>Table of Contents</h2>
                            <ol>
                                <li><a href="#services">Our Services</a></li>
                                <li><a href="#ip">Intellectual Property Rights</a></li>
                                <li><a href="#representations">User Representations</a></li>
                                <li><a href="#registration">User Registration</a></li>
                                <li><a href="#prohibited">Prohibited Activities</a></li>
                                <li><a href="#contributions">User Generated Contributions</a></li>
                                <li><a href="#licence">Contribution Licence</a></li>
                                <li><a href="#management">Services Management</a></li>
                                <li><a href="#privacy">Privacy Policy</a></li>
                                <li><a href="#termination">Term and Termination</a></li>
                                <li><a href="#modifications">Modifications and Interruptions</a></li>
                                <li><a href="#governing">Governing Law</a></li>
                                <li><a href="#disputes">Dispute Resolution</a></li>
                                <li><a href="#corrections">Corrections</a></li>
                                <li><a href="#disclaimer">Disclaimer</a></li>
                                <li><a href="#liability">Limitations of Liability</a></li>
                                <li><a href="#indemnification">Indemnification</a></li>
                                <li><a href="#userdata">User Data</a></li>
                                <li><a href="#electronic">Electronic Communications, Transactions, and Signatures</a></li>
                                <li><a href="#miscellaneous">Miscellaneous</a></li>
                                <li><a href="#contact">Contact Us</a></li>
                            </ol>

                            <h2 id="services">1. Our Services</h2>
                            <p>
                                The information provided when using the Services is not intended for distribution to or use by any person or entity in any jurisdiction or country where such distribution or use would be contrary to law or regulation or which would subject us to any registration requirement within such jurisdiction or country. Accordingly, those persons who choose to access the Services from other locations do so on their own initiative and are solely responsible for compliance with local laws, if and to the extent local laws are applicable.
                            </p>
                            <p>
                                The Services are not tailored to comply with industry-specific regulations (e.g. Health Insurance Portability and Accountability Act (HIPAA), Federal Information Security Management Act (FISMA), etc.), so if your interactions would be subjected to such laws, you may not use the Services. You may not use the Services in a way that would violate the Gramm-Leach-Bliley Act (GLBA).
                            </p>

                            <h2 id="ip">2. Intellectual Property Rights</h2>
                            <p>
                                We are the owner or the licensee of all intellectual property rights in our Services, including all source code, databases, functionality, software, website designs, audio, video, text, photographs, and graphics in the Services (collectively, the 'Content'), as well as the trademarks, service marks, and logos contained therein (the 'Marks'). Our Content and Marks are protected by copyright and trademark laws (and various other intellectual property rights and unfair competition laws) and treaties around the world.
                            </p>
                            <p>
                                The Content and Marks are provided in or through the Services 'AS IS' for your personal, non-commercial use or internal business purpose only. Subject to your compliance with these Legal Terms, we grant you a non-exclusive, non-transferable, revocable licence to access the Services and download or print a copy of any portion of the Content to which you have properly gained access, solely for your personal, non-commercial use or internal business purpose.
                            </p>
                            <p>
                                We reserve all rights not expressly granted to you in and to the Services, Content, and Marks. Any breach of these Intellectual Property Rights will constitute a material breach of our Legal Terms and your right to use our Services will terminate immediately.
                            </p>

                            <h2 id="representations">3. User Representations</h2>
                            <p>By using the Services, you represent and warrant that:</p>
                            <ul>
                                <li>All registration information you submit will be true, accurate, current, and complete</li>
                                <li>You will maintain the accuracy of such information and promptly update such registration information as necessary</li>
                                <li>You have the legal capacity and you agree to comply with these Legal Terms</li>
                                <li>You are not a minor in the jurisdiction in which you reside</li>
                                <li>You will not access the Services through automated or non-human means, whether through a bot, script, or otherwise</li>
                                <li>You will not use the Services for any illegal or unauthorised purpose</li>
                                <li>Your use of the Services will not violate any applicable law or regulation</li>
                            </ul>
                            <p>If you provide any information that is untrue, inaccurate, not current, or incomplete, we have the right to suspend or terminate your account and refuse any and all current or future use of the Services (or any portion thereof).</p>

                            <h2 id="registration">4. User Registration</h2>
                            <p>
                                You may be required to register to use the Services. You agree to keep your password confidential and will be responsible for all use of your account and password. We reserve the right to remove, reclaim, or change a username you select if we determine, in our sole discretion, that such username is inappropriate, obscene, or otherwise objectionable.
                            </p>

                            <h2 id="prohibited">5. Prohibited Activities</h2>
                            <p>You may not access or use the Services for any purpose other than that for which we make the Services available. The Services may not be used in connection with any commercial endeavours except those that are specifically endorsed or approved by us.</p>
                            <p>As a user of the Services, you agree not to:</p>
                            <ul>
                                <li>Systematically retrieve data or other content from the Services to create or compile, directly or indirectly, a collection, compilation, database, or directory without written permission from us</li>
                                <li>Trick, defraud, or mislead us and other users, especially in any attempt to learn sensitive account information such as user passwords</li>
                                <li>Circumvent, disable, or otherwise interfere with security-related features of the Services</li>
                                <li>Disparage, tarnish, or otherwise harm, in our opinion, us and/or the Services</li>
                                <li>Use any information obtained from the Services in order to harass, abuse, or harm another person</li>
                                <li>Make improper use of our support services or submit false reports of abuse or misconduct</li>
                                <li>Use the Services in a manner inconsistent with any applicable laws or regulations</li>
                                <li>Engage in unauthorised framing of or linking to the Services</li>
                                <li>Upload or transmit (or attempt to upload or to transmit) viruses, Trojan horses, or other material that interferes with any party's uninterrupted use and enjoyment of the Services</li>
                                <li>Engage in any automated use of the system, such as using scripts to send comments or messages, or using any data mining, robots, or similar data gathering and extraction tools</li>
                                <li>Delete the copyright or other proprietary rights notice from any Content</li>
                                <li>Attempt to impersonate another user or person or use the username of another user</li>
                                <li>Sell or otherwise transfer your profile</li>
                                <li>Upload or transmit (or attempt to upload or to transmit) any material that acts as a passive or active information collection or transmission mechanism</li>
                                <li>Interfere with, disrupt, or create an undue burden on the Services or the networks or services connected to the Services</li>
                                <li>Harass, annoy, intimidate, or threaten any of our employees or agents engaged in providing any portion of the Services to you</li>
                                <li>Attempt to bypass any measures of the Services designed to prevent or restrict access to the Services, or any portion of the Services</li>
                                <li>Copy or adapt the Services' software, including but not limited to Flash, PHP, HTML, JavaScript, or other code</li>
                                <li>Use the Services as part of any effort to compete with us or otherwise use the Services and/or the Content for any revenue-generating endeavour or commercial enterprise</li>
                            </ul>

                            <h2 id="contributions">6. User Generated Contributions</h2>
                            <p>
                                The Services may invite you to chat, contribute to, or participate in blogs, message boards, online forums, and other functionality, and may provide you with the opportunity to create, submit, post, display, transmit, perform, publish, distribute, or broadcast content and materials to us or on the Services, including but not limited to text, writings, video, audio, photographs, graphics, comments, suggestions, or personal information or other material ('Contributions').
                            </p>
                            <p>
                                Contributions may be viewable by other users of the Services and through third-party websites. When you create or make available any Contributions, you thereby represent and warrant that your Contributions are not false, inaccurate, or misleading; do not violate any applicable law, regulation, or rule; and do not violate the privacy or publicity rights of any third party.
                            </p>

                            <h2 id="licence">7. Contribution Licence</h2>
                            <p>
                                By posting your Contributions to any part of the Services, you automatically grant, and you represent and warrant that you have the right to grant, to us an unrestricted, unlimited, irrevocable, perpetual, non-exclusive, transferable, royalty-free, fully-paid, worldwide right and licence to host, use, copy, reproduce, disclose, sell, resell, publish, broadcast, retitle, archive, store, cache, publicly perform, publicly display, reformat, translate, transmit, excerpt (in whole or in part), and distribute such Contributions for any purpose, commercial, advertising, or otherwise, and to prepare derivative works of, or incorporate into other works, such Contributions.
                            </p>
                            <p>
                                This licence will apply to any form, media, or technology now known or hereafter developed, and includes our use of your name, company name, and franchise name, as applicable, and any of the trademarks, service marks, trade names, logos, and personal and commercial images you provide.
                            </p>

                            <h2 id="management">8. Services Management</h2>
                            <p>We reserve the right, but not the obligation, to:</p>
                            <ul>
                                <li>Monitor the Services for violations of these Legal Terms</li>
                                <li>Take appropriate legal action against anyone who, in our sole discretion, violates the law or these Legal Terms, including without limitation, reporting such user to law enforcement authorities</li>
                                <li>In our sole discretion and without limitation, refuse, restrict access to, limit the availability of, or disable (to the extent technologically feasible) any of your Contributions or any portion thereof</li>
                                <li>In our sole discretion and without limitation, notice, or liability, to remove from the Services or otherwise disable all files and content that are excessive in size or are in any way burdensome to our systems</li>
                                <li>Otherwise manage the Services in a manner designed to protect our rights and property and to facilitate the proper functioning of the Services</li>
                            </ul>

                            <h2 id="privacy">9. Privacy Policy</h2>
                            <p>
                                We care about data privacy and security. Please review our <a href="/privacy">Privacy Policy</a>. By using the Services, you agree to be bound by our Privacy Policy, which is incorporated into these Legal Terms. Please be advised the Services are hosted in the United Kingdom. If you access the Services from any other region of the world with laws or other requirements governing personal data collection, use, or disclosure that differ from applicable laws in the United Kingdom, then through your continued use of the Services, you are transferring your data to the United Kingdom, and you expressly consent to have your data transferred to and processed in the United Kingdom.
                            </p>

                            <h2 id="termination">10. Term and Termination</h2>
                            <p>
                                These Legal Terms shall remain in full force and effect while you use the Services. Without limiting any other provision of these Legal Terms, we reserve the right to, in our sole discretion and without notice or liability, deny access to and use of the Services (including blocking certain IP addresses), to any person for any reason or for no reason, including without limitation for breach of any representation, warranty, or covenant contained in these Legal Terms or of any applicable law or regulation.
                            </p>
                            <p>
                                If we terminate or suspend your account for any reason, you are prohibited from registering and creating a new account under your name, a fake or borrowed name, or the name of any third party, even if you may be acting on behalf of the third party. In addition to terminating or suspending your account, we reserve the right to take appropriate legal action, including without limitation pursuing civil, criminal, and injunctive redress.
                            </p>

                            <h2 id="modifications">11. Modifications and Interruptions</h2>
                            <p>
                                We reserve the right to change, modify, or remove the contents of the Services at any time or for any reason at our sole discretion without notice. We also reserve the right to modify or discontinue all or part of the Services without notice at any time. We will not be liable to you or any third party for any modification, suspension, or discontinuance of the Services.
                            </p>
                            <p>
                                We cannot guarantee the Services will be available at all times. We may experience hardware, software, or other problems or need to perform maintenance related to the Services, resulting in interruptions, delays, or errors. We reserve the right to change, revise, update, suspend, discontinue, or otherwise modify the Services at any time or for any reason without notice to you.
                            </p>

                            <h2 id="governing">12. Governing Law</h2>
                            <p>
                                These Legal Terms are governed by and interpreted following the laws of the United Kingdom, and the use of the United Nations Convention of Contracts for the International Sales of Goods is expressly excluded. If your habitual residence is in the EU, and you are a consumer, you additionally possess the protection provided to you by obligatory provisions of the law in your country to residence. Black Professionals United Kingdom and yourself both agree to submit to the non-exclusive jurisdiction of the courts of Scotland, which means that you may make a claim to defend your consumer protection rights in regards to these Legal Terms in the UK or in the EU country in which you reside.
                            </p>

                            <h2 id="disputes">13. Dispute Resolution</h2>
                            <p>
                                <strong>Informal Negotiations.</strong> To expedite resolution and control the cost of any dispute, controversy, or claim related to these Legal Terms (each a 'Dispute' and collectively, the 'Disputes'), the Parties agree to first attempt to negotiate any Dispute (except those Disputes expressly provided below) informally for at least thirty (30) days before initiating arbitration. Such informal negotiations commence upon written notice from one Party to the other Party.
                            </p>
                            <p>
                                <strong>Binding Arbitration.</strong> If the Parties are unable to resolve a Dispute through informal negotiations, the Dispute (except those Disputes expressly excluded below) will be finally and exclusively resolved by binding arbitration. The arbitration shall be commenced and conducted under the Chartered Institute of Arbitrators rules. The arbitration may be conducted in person, through the submission of documents, by phone, or online. The arbitrator will make a decision in writing but need not provide a statement of reasons unless requested by either Party. The arbitrator must follow applicable law.
                            </p>
                            <p>
                                <strong>Restrictions.</strong> The Parties agree that any arbitration shall be limited to the Dispute between the Parties individually. To the full extent permitted by law, no arbitration shall be joined with any other proceeding; there is no right or authority for any Dispute to be arbitrated on a class-action basis or to utilise class action procedures; and there is no right or authority for any Dispute to be brought in a purported representative capacity on behalf of the general public or any other persons.
                            </p>

                            <h2 id="corrections">14. Corrections</h2>
                            <p>
                                There may be information on the Services that contains typographical errors, inaccuracies, or omissions, including descriptions, pricing, availability, and various other information. We reserve the right to correct any errors, inaccuracies, or omissions and to change or update the information on the Services at any time, without prior notice.
                            </p>

                            <h2 id="disclaimer">15. Disclaimer</h2>
                            <p className="uppercase font-semibold text-sm">
                                THE SERVICES ARE PROVIDED ON AN AS-IS AND AS-AVAILABLE BASIS. YOU AGREE THAT YOUR USE OF THE SERVICES WILL BE AT YOUR SOLE RISK. TO THE FULLEST EXTENT PERMITTED BY LAW, WE DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED, IN CONNECTION WITH THE SERVICES AND YOUR USE THEREOF, INCLUDING, WITHOUT LIMITATION, THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. WE MAKE NO WARRANTIES OR REPRESENTATIONS ABOUT THE ACCURACY OR COMPLETENESS OF THE SERVICES' CONTENT OR THE CONTENT OF ANY WEBSITES OR MOBILE APPLICATIONS LINKED TO THE SERVICES AND WE WILL ASSUME NO LIABILITY OR RESPONSIBILITY FOR ANY ERRORS, MISTAKES, OR INACCURACIES OF CONTENT AND MATERIALS.
                            </p>

                            <h2 id="liability">16. Limitations of Liability</h2>
                            <p className="uppercase font-semibold text-sm">
                                IN NO EVENT WILL WE OR OUR DIRECTORS, EMPLOYEES, OR AGENTS BE LIABLE TO YOU OR ANY THIRD PARTY FOR ANY DIRECT, INDIRECT, CONSEQUENTIAL, EXEMPLARY, INCIDENTAL, SPECIAL, OR PUNITIVE DAMAGES, INCLUDING LOST PROFIT, LOST REVENUE, LOSS OF DATA, OR OTHER DAMAGES ARISING FROM YOUR USE OF THE SERVICES, EVEN IF WE HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
                            </p>

                            <h2 id="indemnification">17. Indemnification</h2>
                            <p>
                                You agree to defend, indemnify, and hold us harmless, including our subsidiaries, affiliates, and all of our respective officers, agents, partners, and employees, from and against any loss, damage, liability, claim, or demand, including reasonable attorneys' fees and expenses, made by any third party due to or arising out of: (1) your Contributions; (2) use of the Services; (3) breach of these Legal Terms; (4) any breach of your representations and warranties set forth in these Legal Terms; (5) your violation of the rights of a third party, including but not limited to intellectual property rights; or (6) any overt harmful act toward any other user of the Services with whom you connected via the Services.
                            </p>

                            <h2 id="userdata">18. User Data</h2>
                            <p>
                                We will maintain certain data that you transmit to the Services for the purpose of managing the performance of the Services, as well as data relating to your use of the Services. Although we perform regular routine backups of data, you are solely responsible for all data that you transmit or that relates to any activity you have undertaken using the Services. You agree that we shall have no liability to you for any loss or corruption of any such data, and you hereby waive any right of action against us arising from any such loss or corruption of such data.
                            </p>

                            <h2 id="electronic">19. Electronic Communications, Transactions, and Signatures</h2>
                            <p>
                                Visiting the Services, sending us emails, and completing online forms constitute electronic communications. You consent to receive electronic communications, and you agree that all agreements, notices, disclosures, and other communications we provide to you electronically, via email and on the Services, satisfy any legal requirement that such communication be in writing. You hereby agree to the use of electronic signatures, contracts, orders, and other records, and to electronic delivery of notices, policies, and records of transactions initiated or completed by us or via the Services.
                            </p>

                            <h2 id="miscellaneous">20. Miscellaneous</h2>
                            <p>
                                These Legal Terms and any policies or operating rules posted by us on the Services or in respect to the Services constitute the entire agreement and understanding between you and us. Our failure to exercise or enforce any right or provision of these Legal Terms shall not operate as a waiver of such right or provision. These Legal Terms operate to the fullest extent permissible by law. We may assign any or all of our rights and obligations to others at any time. We shall not be responsible or liable for any loss, damage, delay, or failure to act caused by any cause beyond our reasonable control. If any provision or part of a provision of these Legal Terms is determined to be unlawful, void, or unenforceable, that provision or part of the provision is deemed severable from these Legal Terms and does not affect the validity and enforceability of any remaining provisions. There is no joint venture, partnership, employment, or agency relationship created between you and us as a result of these Legal Terms or use of the Services. You agree that these Legal Terms will not be construed against us by virtue of having drafted them.
                            </p>

                            <h2 id="contact">21. Contact Us</h2>
                            <p>
                                In order to resolve a complaint regarding the Services or to receive further information regarding use of the Services, please contact us at:
                            </p>
                            <address>
                                Black Professionals United Kingdom<br />
                                5 South Charlotte Street<br />
                                Edinburgh EH2 4AN<br />
                                United Kingdom<br />
                                <a href="mailto:info@blackprofessionals.uk">info@blackprofessionals.uk</a>
                            </address>

                        </div>
                    </div>
                </section>
            </main>
                <SiteFooter />
        </div>
    );
}
