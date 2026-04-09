import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ auth, users }) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Gestión de Usuarios (Tenant)
                    </h2>
                    <Link href={route('users.create')}>
                        <PrimaryButton>Nuevo Usuario</PrimaryButton>
                    </Link>
                </div>
            }
        >
            <Head title="Usuarios" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="border-b dark:border-gray-700 uppercase text-xs text-gray-500 dark:text-gray-400">
                                        <th className="px-4 py-3">Nombre</th>
                                        <th className="px-4 py-3">Correo</th>
                                        <th className="px-4 py-3">Rol Spatie</th>
                                        <th className="px-4 py-3">Dependencia Operativa</th>
                                        <th className="px-4 py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {users.map((user) => (
                                        <tr key={user.id} className="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                            <td className="px-4 py-3 font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                                {user.name}
                                            </td>
                                            <td className="px-4 py-3 text-gray-500 dark:text-gray-400">
                                                {user.email}
                                            </td>
                                            <td className="px-4 py-3">
                                                {user.roles && user.roles.length > 0 
                                                    ? <span className="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{user.roles[0].name}</span>
                                                    : <span className="text-gray-400 italic">Sin rol</span>
                                                }
                                            </td>
                                            <td className="px-4 py-3 text-gray-600 dark:text-gray-300">
                                                {user.department?.name || <span className="italic text-gray-400">— Central —</span>}
                                            </td>
                                            <td className="px-4 py-3 text-right">
                                                <Link className="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">Editar</Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>

                            {users.length === 0 && (
                                <div className="text-center py-8 text-gray-500">No hay usuarios registrados aparte de ti.</div>
                            )}

                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
