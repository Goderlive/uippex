import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, Link } from '@inertiajs/react';

export default function Dashboard() {
    const { tenantId, municipal_config } = usePage().props;
    const primaryColor = municipal_config?.primary_color || '#6366f1'; // fallback to indigo-500

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Cockpit Principal PBRM - <span className="capitalize" style={{ color: primaryColor }}>{municipal_config?.official_name || tenantId || 'Desconocido'}</span>
                </h2>
            }
        >
            <Head title={`Dashboard - ${municipal_config?.official_name || tenantId || 'SaaS'}`} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Welcome Card */}
                    <div 
                        className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 mb-6 border-l-4"
                        style={{ borderLeftColor: primaryColor }}
                    >
                        <div className="p-6 text-gray-900 dark:text-gray-100 flex items-center justify-between">
                            <div>
                                <h3 className="text-lg font-bold">¡Bienvenido al panel centralizado!</h3>
                                <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Este es tu entorno protegido y aislado de administración municipal.</p>
                            </div>
                            <div className="text-right">
                                <span 
                                    className="px-3 py-1 text-xs font-semibold rounded-full bg-opacity-20"
                                    style={{ backgroundColor: `${primaryColor}20`, color: primaryColor }}
                                >
                                    Conexión Segura (Tenant DB)
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Structural Dashboard Grid Placeholder */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                         <div className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                              <h4 className="text-gray-500 dark:text-gray-400 text-sm font-medium">Actividades Sustantivas Activas</h4>
                              <p className="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">--</p>
                         </div>
                         <div className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                              <h4 className="text-gray-500 dark:text-gray-400 text-sm font-medium">Avance Financiero General</h4>
                              <p className="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">0.0%</p>
                         </div>
                         <div className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                              <h4 className="text-gray-500 dark:text-gray-400 text-sm font-medium">Semáforo de Cumplimiento OSFEM</h4>
                              <div className="mt-2 flex items-center space-x-2">
                                   <div className="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                                   <span className="text-lg font-bold text-gray-900 dark:text-gray-100">Óptimo</span>
                              </div>
                         </div>
                         <div className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-emerald-700 flex flex-col justify-between">
                              <div>
                                  <h4 className="text-gray-500 dark:text-gray-400 text-sm font-medium">Configuración de Accesos</h4>
                                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-2">Gestiona el talento humano, enlaces y niveles de revisión.</p>
                              </div>
                              <Link href={route('users.index')} className="mt-4 inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                  Administrar Usuarios
                              </Link>
                         </div>
                         <div className="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-emerald-100 dark:border-emerald-700 flex flex-col justify-between">
                              <div>
                                  <h4 className="text-gray-500 dark:text-gray-400 text-sm font-medium">Importador TXT PBR</h4>
                                  <p className="text-sm text-gray-600 dark:text-gray-400 mt-2">Carga la ramificación inicial de actividades desde Legacy.</p>
                              </div>
                              <Link href={route('settings.import-txt')} className="mt-4 inline-flex items-center justify-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 focus:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                  Abrir Importador
                              </Link>
                         </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
