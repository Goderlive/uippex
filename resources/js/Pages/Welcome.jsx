import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome({ auth }) {
    const { municipal_config } = usePage().props;

    const primaryColor = municipal_config?.primary_color || '#3b82f6'; // Fallback to blue-500

    return (
        <>
            <Head title={municipal_config?.official_name ? `${municipal_config.official_name} - UIPPEX` : 'UIPPEX - Inicio'} />

            {/* Main Wrapper with Background */}
            <div className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-slate-950 px-6 py-12 selection:bg-blue-500 selection:text-white">

                {/* Subtle Radial Gradient Background */}
                <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(17,24,39,1)_0%,rgba(2,6,23,1)_100%)] opacity-80" />

                {/* Decorative background elements using dynamic primary color */}
                <div 
                    className="pointer-events-none absolute -left-20 -top-20 h-96 w-96 rounded-full blur-[120px] opacity-20"
                    style={{ backgroundColor: primaryColor }}
                />
                <div 
                    className="pointer-events-none absolute -right-20 -bottom-20 h-96 w-96 rounded-full blur-[120px] opacity-20"
                    style={{ backgroundColor: primaryColor }}
                />

                <div className="relative z-10 w-full max-w-4xl text-center">
                    {/* Header/Nav */}
                    <nav className="absolute -top-20 left-0 right-0 flex justify-end gap-6 px-4 md:-top-28">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="text-sm font-medium text-slate-400 transition hover:text-white"
                            >
                                Panel de Control
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="text-sm font-medium text-slate-400 transition hover:text-white underline-offset-4 hover:underline"
                                >
                                    Entrar
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="text-sm font-medium text-slate-400 transition hover:text-white underline-offset-4 hover:underline"
                                >
                                    Registro
                                </Link>
                            </>
                        )}
                    </nav>

                    {/* Municipality Branding - Main priority */}
                    <header className="mb-12 flex flex-col items-center animate-in fade-in slide-in-from-bottom-8 duration-700">
                        {municipal_config?.logo_url ? (
                            <div className="mb-8 p-4 bg-white/5 rounded-3xl shadow-2xl ring-1 ring-white/10 max-w-[280px]">
                                <img 
                                    src={municipal_config.logo_url} 
                                    alt={municipal_config.official_name} 
                                    className="h-28 sm:h-32 w-auto object-contain"
                                />
                            </div>
                        ) : (
                            <div className="mb-8 flex h-24 w-24 items-center justify-center rounded-3xl bg-slate-900 shadow-2xl ring-1 ring-white/10">
                                <svg className="h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        )}

                        <h1 className="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            {municipal_config?.official_name || 'H. Ayuntamiento Constitucional'}
                        </h1>
                        
                        {municipal_config?.administration_period && (
                            <p className="mt-3 text-sm font-semibold uppercase tracking-widest text-slate-400">
                                Administración {municipal_config.administration_period}
                            </p>
                        )}

                        {/* UIPPEX Brand Integrated Elegantly as Platform */}
                        <div className="mt-8 inline-flex items-center gap-3 rounded-full bg-slate-900/60 px-5 py-2.5 ring-1 ring-white/10 backdrop-blur-md">
                            <img 
                                src="/images/uippex-logo.png" 
                                alt="UIPPEX Logo" 
                                className="h-6 w-6 object-contain rounded-md"
                            />
                            <span className="text-sm font-medium text-slate-300">
                                UIPPEX <span className="text-slate-500 font-normal">| Planeación, Programación y Evaluación</span>
                            </span>
                        </div>
                    </header>

                    {/* CTA Section */}
                    <div className="flex flex-col items-center justify-center gap-4 animate-in fade-in slide-in-from-bottom-12 duration-1000 delay-300">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="group relative flex h-14 w-full min-w-[240px] items-center justify-center overflow-hidden rounded-xl px-8 shadow-lg text-white font-semibold hover:opacity-90 active:scale-95 transition-all duration-200"
                                style={{ backgroundColor: primaryColor }}
                            >
                                Ir al Panel de Control
                            </Link>
                        ) : (
                            <div className="flex w-full flex-col gap-4 sm:w-auto sm:flex-row">
                                <Link
                                    href={route('login')}
                                    className="group relative flex h-14 w-full min-w-[200px] items-center justify-center overflow-hidden rounded-xl px-8 shadow-lg text-white font-semibold hover:opacity-90 active:scale-95 transition-all duration-200"
                                    style={{ backgroundColor: primaryColor }}
                                >
                                    Iniciar Sesión
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="group flex h-14 w-full min-w-[200px] items-center justify-center rounded-xl bg-slate-900 px-8 ring-1 ring-white/10 transition-all hover:bg-slate-800 active:scale-[0.98] text-white font-semibold"
                                >
                                    Explorar Registro
                                </Link>
                            </div>
                        )}
                        <p className="mt-8 text-xs font-medium uppercase tracking-[0.2em] text-slate-600">
                            Innovación en Gestión Pública Municipal
                        </p>
                    </div>
                </div>

                {/* Simplified Footer */}
                <footer className="absolute bottom-12 w-full text-center">
                    <p className="text-xs font-medium text-slate-500">
                        &copy; {new Date().getFullYear()} {municipal_config?.official_name || 'H. Ayuntamiento'}. Desarrollado con UIPPEX.
                    </p>
                </footer>
            </div>
        </>
    );
}
