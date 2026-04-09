import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function AreaActivitiesPlaceholder({ unit, message }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Panel de Trabajo: {unit.name}
                </h2>
            }
        >
            <Head title={unit.name} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                        <div className="p-12 text-center">
                            <div className="inline-flex items-center justify-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-full text-yellow-600 dark:text-yellow-400 mb-6 font-bold text-lg border-2 border-yellow-200/50 dark:border-yellow-700/50">
                                🚧 Módulo en Construcción 🚧
                            </div>
                            
                            <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                {message}
                            </h3>
                            
                            <p className="text-gray-600 dark:text-gray-400 max-w-lg mx-auto mb-8">
                                Estás viendo el punto de entrada para la gestión física de metas del área. 
                                En la siguiente fase, aquí aparecerá la tabla interactiva para capturar los avances mensuales de tus Actividades Sustantivas.
                            </p>

                            <div className="flex justify-center space-x-4">
                                <Link 
                                    href={route('activities.department.show', unit.department_id)}
                                    className="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition-colors"
                                >
                                    Volver a la Lista
                                </Link>
                                
                                <button
                                    onClick={() => alert('Próximamente: Apertura de captura mensual.')}
                                    className="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium shadow-sm transition-colors"
                                >
                                    Saber más
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
