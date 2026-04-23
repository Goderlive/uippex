import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function AreaList({ areas, current_department_name, ramt_quarters_compliance, is_enlace }) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col">
                    <span className="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-1">
                        Dependencia: {current_department_name}
                    </span>
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Áreas Operativas (Unidades Administrativas)
                    </h2>
                </div>
            }
        >
            <Head title={`Áreas - ${current_department_name}`} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    {/* Fase 14.2: Sección Descargas y Constancias Departamentales (RAMT) */}
                    {is_enlace && ramt_quarters_compliance && (
                        <div className="mb-8 bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-indigo-200 dark:border-indigo-900 border-l-4 border-l-indigo-500">
                            <h3 className="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest mb-3">
                                Emitir Constancias Oficiales (Acuses Trimestrales)
                            </h3>
                            <div className="flex flex-wrap gap-3">
                                {[1, 2, 3, 4].map((q) => {
                                    const isValidated = ramt_quarters_compliance[q];
                                    return isValidated ? (
                                        <a
                                            key={`q-${q}`}
                                            target="_blank"
                                            href={route('activities.ramt.download', { quarter: q })}
                                            className="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out shadow-sm"
                                            title={`Descargar Constancia Departamental Trimestre ${q}`}
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Generar RAMT T{q}
                                        </a>
                                    ) : (
                                        <div
                                            key={`q-${q}`}
                                            className="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest cursor-not-allowed pointer-events-none"
                                            title={`Bloqueado: La Dependencia no ha completado el 100% de reportes validados para T${q}.`}
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Bloqueado T{q}
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    <div className="mb-8 flex items-center justify-between">
                        <Link 
                            href={route('activities.index')}
                            className="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-300 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver al Directorio
                        </Link>
                    </div>

                    <div className="grid grid-cols-1 gap-4">
                        {areas.map((area) => (
                            <Link 
                                key={area.id} 
                                href={route('activities.area.show', area.id)}
                                className="block group"
                            >
                                <div className="p-5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-indigo-400 dark:group-hover:border-indigo-500 overflow-hidden relative">
                                    <div className="flex items-center">
                                        <div className="flex-shrink-0 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg text-gray-400 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/40 group-hover:text-indigo-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div className="ml-5">
                                            <h4 className="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {area.name}
                                            </h4>
                                            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Cve: {area.general_sector_code}-{area.auxiliary_sector_code} | Gestionar metas trimestrales
                                            </p>
                                        </div>
                                        <div className="ml-auto flex items-center">
                                            <span className="text-xs bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-full border border-green-100 dark:border-green-800/50 mr-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                                Configurado OK
                                            </span>
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 text-gray-300 group-hover:text-indigo-500 transform group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div className="absolute top-0 right-0 w-2 h-full bg-indigo-500 transform translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                                </div>
                            </Link>
                        ))}
                    </div>

                    {areas.length === 0 && (
                        <div className="bg-white dark:bg-gray-800 p-12 text-center rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                            <p className="text-gray-500 italic">Esta dependencia aún no cuenta con Unidades Administrativas registradas en este Año Fiscal.</p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
