import { Head, Link } from '@inertiajs/react';

export default function Welcome({ auth, laravelVersion, phpVersion }) {
    return (
        <>
            <Head title="UIPPEX - Inicio" />
            
            {/* Main Wrapper with Background */}
            <div className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-slate-950 px-6 py-12 selection:bg-blue-500 selection:text-white">
                
                {/* Subtle Radial Gradient Background */}
                <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(17,24,39,1)_0%,rgba(2,6,23,1)_100%)] opacity-80" />
                
                {/* Decorative background elements */}
                <div className="pointer-events-none absolute -left-20 -top-20 h-96 w-96 rounded-full bg-blue-500/10 blur-[100px]" />
                <div className="pointer-events-none absolute -right-20 -bottom-20 h-96 w-96 rounded-full bg-teal-500/10 blur-[100px]" />

                <div className="relative z-10 w-full max-w-4xl text-center">
                    {/* Header/Nav */}
                    <nav className="absolute -top-32 left-0 right-0 flex justify-end gap-6 px-4 md:-top-40">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="text-sm font-medium text-slate-400 transition hover:text-white"
                            >
                                Dashboard
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

                    {/* Logo & Branding */}
                    <header className="mb-12 animate-in fade-in slide-in-from-bottom-8 duration-700">
                        <div className="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-teal-500 shadow-2xl shadow-blue-500/20 ring-1 ring-white/20">
                            <span className="text-4xl font-black text-white">U</span>
                        </div>
                        <h1 className="text-6xl font-black tracking-tight text-white sm:text-7xl lg:text-8xl">
                            UIPPE<span className="bg-gradient-to-r from-blue-400 to-teal-400 bg-clip-text text-transparent">X</span>
                        </h1>
                        <p className="mt-6 text-xl font-light tracking-wide text-slate-400 sm:text-2xl">
                            Sistema Integral de Planeación y Gestión Estatal
                        </p>
                    </header>

                    {/* CTA Section */}
                    <div className="flex flex-col items-center justify-center gap-4 animate-in fade-in slide-in-from-bottom-12 duration-1000 delay-300">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="group relative flex h-14 w-full items-center justify-center overflow-hidden rounded-xl bg-white px-8 transition-transform active:scale-[0.98] sm:w-auto"
                            >
                                <span className="text-lg font-semibold text-slate-950 transition-colors group-hover:text-blue-600">
                                    Ir al Panel de Control
                                </span>
                            </Link>
                        ) : (
                            <div className="flex w-full flex-col gap-4 sm:w-auto sm:flex-row">
                                <Link
                                    href={route('login')}
                                    className="group relative flex h-14 w-full min-w-[200px] items-center justify-center overflow-hidden rounded-xl bg-white px-8 transition-transform active:scale-[0.98]"
                                >
                                    <span className="text-lg font-semibold text-slate-950 transition-colors group-hover:text-blue-600 text-center">
                                        Iniciar Sesión
                                    </span>
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="group flex h-14 w-full min-w-[200px] items-center justify-center rounded-xl bg-slate-900 px-8 ring-1 ring-white/10 transition-all hover:bg-slate-800 active:scale-[0.98]"
                                >
                                    <span className="text-lg font-semibold text-white">
                                        Explorar Registro
                                    </span>
                                </Link>
                            </div>
                        )}
                        <p className="mt-8 text-xs font-medium uppercase tracking-[0.2em] text-slate-600">
                            Innovación en Gestión Pública
                        </p>
                    </div>
                </div>

                {/* Simplified Footer */}
                <footer className="absolute bottom-12 w-full text-center">
                    <p className="text-xs font-medium text-slate-500">
                        &copy; {new Date().getFullYear()} UIPPEX. Todos los derechos reservados.
                    </p>
                </footer>
            </div>
        </>
    );
}
