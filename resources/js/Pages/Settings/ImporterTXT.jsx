import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function ImporterTXT({ auth, flash = {} }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        txt_file: null,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('settings.import-txt.store'), {
            onSuccess: () => reset('txt_file'),
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Importador TXT PBR</h2>}
        >
            <Head title="Importar TXT Inquilino" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {flash.message && (
                        <div className="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span className="block sm:inline">{flash.message}</span>
                        </div>
                    )}
                    
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Carga de Estructura Programática (Formato Pipe-Delimited)
                            </h3>
                            <p className="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Sube el archivo TXT extraído del Sistema Legacy central para cargar de forma automática la estructura de las áreas y ramas sustantivas al Inquilino.
                            </p>

                            <form onSubmit={submit} className="space-y-6 max-w-xl">
                                <div>
                                    <label htmlFor="txt_file" className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Archivo TXT Legacy
                                    </label>
                                    <div className="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                                        <div className="space-y-1 text-center">
                                            <svg className="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            <div className="flex text-sm text-gray-600 dark:text-gray-400">
                                                <label htmlFor="file-upload" className="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Sube un archivo</span>
                                                    <input id="file-upload" name="file-upload" type="file" className="sr-only" onChange={e => setData('txt_file', e.target.files[0])} />
                                                </label>
                                                <p className="pl-1">o arrastra y suelta</p>
                                            </div>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                                TXT hasta 10MB
                                            </p>
                                        </div>
                                    </div>
                                    {data.txt_file && (
                                        <p className="mt-2 text-sm text-green-600 dark:text-green-400">
                                            Seleccionado: {data.txt_file.name}
                                        </p>
                                    )}
                                    {errors.txt_file && (
                                        <p className="mt-2 text-sm text-red-600 dark:text-red-400">{errors.txt_file}</p>
                                    )}
                                </div>

                                <div className="flex items-center justify-end">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    >
                                        Importar Datos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
