import React, { useRef } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';

export default function Configuration({ settings, logo_url, shield_url }) {
    const { auth } = usePage().props;
    const logoInputRef = useRef();
    const shieldInputRef = useRef();

    const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
        official_name: settings.official_name || 'H. Ayuntamiento',
        administration_period: settings.administration_period || '',
        primary_color: settings.primary_color || '#333333',
        logo: null,
        shield: null,
    });

    const submit = (e) => {
        e.preventDefault();
        
        post(route('premium.configuration.update'), {
            preserveScroll: true,
            // We use POST with file uploads to easily handle multipart/form-data
            // rather than dealing with PUT method spoofing complications
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Identidad y Configuración del SaaS</h2>}
        >
            <Head title="Identidad Institucional" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <section className="max-w-xl">
                            <header>
                                <h2 className="text-lg font-medium text-gray-900">Perfil Inquilino</h2>
                                <p className="mt-1 text-sm text-gray-600">
                                    Actualice la información visual y estética de su municipio en la plataforma.
                                    Esta configuración definirá colores y logotipos en reportes PDF y UI generados.
                                </p>
                            </header>

                            <form onSubmit={submit} className="mt-6 space-y-6" encType="multipart/form-data">
                                <div>
                                    <label htmlFor="official_name" className="block font-medium text-sm text-gray-700">Nombre Oficial del Municipio</label>
                                    <input
                                        id="official_name"
                                        type="text"
                                        className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        value={data.official_name}
                                        onChange={(e) => setData('official_name', e.target.value)}
                                        required
                                        maxLength={100}
                                    />
                                    {errors.official_name && <p className="text-sm text-red-600 mt-2">{errors.official_name}</p>}
                                </div>

                                <div>
                                    <label htmlFor="administration_period" className="block font-medium text-sm text-gray-700">Periodo de Administración</label>
                                    <input
                                        id="administration_period"
                                        type="text"
                                        placeholder="Ej. 2025 - 2027"
                                        className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        value={data.administration_period}
                                        onChange={(e) => setData('administration_period', e.target.value)}
                                        maxLength={50}
                                    />
                                    {errors.administration_period && <p className="text-sm text-red-600 mt-2">{errors.administration_period}</p>}
                                </div>

                                <div>
                                    <label htmlFor="primary_color" className="block font-medium text-sm text-gray-700">Color Primario Institucional</label>
                                    <div className="flex items-center mt-1">
                                        <input
                                            id="primary_color"
                                            type="color"
                                            className="block h-10 w-14 rounded-md border-gray-300 cursor-pointer shadow-sm"
                                            value={data.primary_color}
                                            onChange={(e) => setData('primary_color', e.target.value)}
                                            required
                                        />
                                        <span className="ml-3 text-sm text-gray-600">{data.primary_color}</span>
                                    </div>
                                    {errors.primary_color && <p className="text-sm text-red-600 mt-2">{errors.primary_color}</p>}
                                </div>

                                {/* Logo Upload */}
                                <div>
                                    <label className="block font-medium text-sm text-gray-700">Logotipo Principal (PNG, JPEG - Max 2MB)</label>
                                    <div className="mt-2 flex items-center space-x-4">
                                        {logo_url && (
                                            <div className="h-20 w-20 flex-shrink-0">
                                                <img src={logo_url} alt="Logo" className="h-full w-full object-contain rounded border bg-gray-50" />
                                            </div>
                                        )}
                                        <input
                                            type="file"
                                            ref={logoInputRef}
                                            accept="image/png, image/jpeg"
                                            className="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-md file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100"
                                            onChange={(e) => setData('logo', e.target.files[0])}
                                        />
                                    </div>
                                    {errors.logo && <p className="text-sm text-red-600 mt-2">{errors.logo}</p>}
                                </div>

                                {/* Shield Upload */}
                                <div>
                                    <label className="block font-medium text-sm text-gray-700">Escudo / Heráldica (PNG, JPEG - Max 2MB)</label>
                                    <div className="mt-2 flex items-center space-x-4">
                                        {shield_url && (
                                            <div className="h-20 w-20 flex-shrink-0">
                                                <img src={shield_url} alt="Escudo" className="h-full w-full object-contain rounded border bg-gray-50" />
                                            </div>
                                        )}
                                        <input
                                            type="file"
                                            ref={shieldInputRef}
                                            accept="image/png, image/jpeg"
                                            className="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-md file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100"
                                            onChange={(e) => setData('shield', e.target.files[0])}
                                        />
                                    </div>
                                    {errors.shield && <p className="text-sm text-red-600 mt-2">{errors.shield}</p>}
                                </div>

                                <div className="flex items-center gap-4">
                                    <button
                                        disabled={processing}
                                        className="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25"
                                    >
                                        Guardar Configuraciones
                                    </button>

                                    {recentlySuccessful && (
                                        <p className="text-sm text-green-600">Guardado exitosamente.</p>
                                    )}
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
