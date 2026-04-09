import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Index({ reconductions, isAdmin }) {
    const { post } = useForm();

    const createDraft = () => {
        post(route('reconductions.store'));
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        {isAdmin ? 'Revisión y Aprobación OSFEM' : 'Mis Dictámenes de Reconducción'}
                    </h2>
                    {!isAdmin && (
                        <button
                            onClick={createDraft}
                            className="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            + Nuevo Oficio (Draft)
                        </button>
                    )}
                </div>
            }
        >
            <Head title="Reconducciones PBR" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            
                            {reconductions.length === 0 ? (
                                <p className="text-gray-500 text-center py-8">No hay documentos generados.</p>
                            ) : (
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead className="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha / Trimestre</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                            {reconductions.map((rec) => (
                                                <tr key={rec.id}>
                                                    <td className="px-6 py-4 whitespace-nowrap font-medium text-indigo-600 dark:text-indigo-400">
                                                        {rec.document_number}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                        {rec.requested_date} <br/> 
                                                        <span className="text-xs text-gray-500 dark:text-gray-400">Trimestre {rec.quarter}</span>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        {rec.administrative_unit?.name || 'Área general'}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap">
                                                        <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            ${rec.status === 0 ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 
                                                              rec.status === 1 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' :
                                                              rec.status === 2 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                                              'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'}`}
                                                        >
                                                            {rec.status === 0 ? 'Borrador' : rec.status === 1 ? 'En Revisión (PMD)' : rec.status === 2 ? 'Aprobado PBR' : 'Rechazado'}
                                                        </span>
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-4">
                                                        <Link 
                                                            href={route('reconductions.edit', rec.id)}
                                                            className="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                        >
                                                            {isAdmin ? 'Revisar Detalle' : 'Editar / Ver Detalle'}
                                                        </Link>
                                                        {rec.status > 0 && (
                                                            <a 
                                                                href={route('reconductions.pdf', rec.id)} 
                                                                target="_blank" 
                                                                rel="noreferrer"
                                                                className="text-rose-600 hover:text-rose-900 dark:text-rose-400 dark:hover:text-rose-300 font-bold"
                                                            >
                                                                📄 Oficio PDF
                                                            </a>
                                                        )}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}

                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
