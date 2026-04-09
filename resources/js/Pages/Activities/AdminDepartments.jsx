import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function AdminDepartments({ departments }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Directorio Institucional
                </h2>
            }
        >
            <Head title="Dependencias" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="mb-6">
                        <p className="text-gray-600 dark:text-gray-400">
                            Selecciona una Dependencia para gestionar sus Áreas Administrativas y Metas.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {departments.map((dept) => (
                            <Link 
                                key={dept.id} 
                                href={route('activities.department.show', dept.id)}
                                className="block group"
                            >
                                <div className="h-full p-6 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 group-hover:border-indigo-500 dark:group-hover:border-indigo-400">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <span className="inline-flex items-center justify-center p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400 mb-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </span>
                                            <h3 className="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {dept.name}
                                            </h3>
                                            <div className="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                <span>Ver Áreas Operativas</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>

                    {departments.length === 0 && (
                        <div className="bg-white dark:bg-gray-800 p-12 text-center rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                            <p className="text-gray-500">No se encontraron dependencias registradas.</p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
