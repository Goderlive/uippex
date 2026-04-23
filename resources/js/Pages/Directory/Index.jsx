import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function DirectoryIndex({ auth, departments, flash }) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [modalContext, setModalContext] = useState(null); // { id_modelo, type_model, title }

    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        id_modelo: '',
        type_model: '',
        academic_degree: '',
        first_name: '',
        last_name: '',
        position_title: '',
    });

    const openModal = (modelId, modelType, title, currentHolder) => {
        setModalContext({ id_modelo: modelId, type_model: modelType, title });
        setData({
            id_modelo: modelId,
            type_model: modelType,
            academic_degree: currentHolder?.academic_degree || '',
            first_name: currentHolder?.first_name || '',
            last_name: currentHolder?.last_name || '',
            position_title: currentHolder?.position_title || '',
        });
        clearErrors();
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        reset();
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('directory.upsert'), {
            onSuccess: () => closeModal(),
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Directorio de Titulares y Jefes Operativos</h2>}
        >
            <Head title="Directorio" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {flash?.success && (
                        <div className="mb-4 font-medium text-sm text-green-600 bg-green-100 p-4 rounded-lg shadow-sm border border-green-200">
                            {flash.success}
                        </div>
                    )}
                    
                    <div className="bg-white overflow-hidden shadow sm:rounded-lg p-6">
                        {departments.length === 0 ? (
                            <div className="text-center py-8 text-gray-500">
                                No se encontraron dependencias asignadas.
                            </div>
                        ) : (
                            <div className="flex flex-col space-y-6">
                                {departments.map((department) => (
                                    <div key={department.id} className="border border-gray-200 rounded-lg p-5 bg-gray-50 shadow-sm transition-shadow hover:shadow-md">
                                        <div className="flex items-center justify-between mb-4">
                                            <div>
                                                <div className="flex items-center space-x-2">
                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 uppercase tracking-wide">
                                                        Dependencia
                                                    </span>
                                                    <h3 className="text-lg font-bold text-gray-900">{department.name}</h3>
                                                </div>
                                                {department.holder ? (
                                                    <div className="mt-3 text-sm text-gray-700 bg-white p-3 rounded border border-gray-100 shadow-sm">
                                                        <p className="font-bold text-gray-900">{department.holder.position_title}</p>
                                                        <p className="text-gray-600 mt-1">{department.holder.academic_degree} {department.holder.first_name} {department.holder.last_name}</p>
                                                    </div>
                                                ) : (
                                                    <div className="mt-3 text-sm text-amber-600 bg-amber-50 p-2 rounded border border-amber-100 italic">
                                                        Sin titular asignado
                                                    </div>
                                                )}
                                            </div>
                                            <button
                                                onClick={() => openModal(department.id, 'Department', department.name, department.holder)}
                                                className="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md text-sm font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                            >
                                                Asignar/Editar Titular
                                            </button>
                                        </div>

                                        {/* Nested Administrative Units */}
                                        {department.administrative_units && department.administrative_units.length > 0 && (
                                            <div className="ml-6 mt-4 space-y-3 border-l-2 border-indigo-200 pl-6">
                                                {department.administrative_units.map((unit) => (
                                                    <div key={unit.id} className="bg-white border border-gray-200 rounded-md p-4 shadow-sm flex items-start sm:items-center justify-between flex-col sm:flex-row hover:border-indigo-300 transition-colors group">
                                                        <div className="mb-3 sm:mb-0">
                                                            <div className="flex items-center space-x-2">
                                                                <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 uppercase tracking-wide">
                                                                    Área
                                                                </span>
                                                                <h4 className="text-md font-semibold text-gray-800">{unit.name}</h4>
                                                            </div>
                                                            {unit.holder ? (
                                                                <div className="mt-2 text-sm text-gray-700">
                                                                    <p className="font-semibold text-gray-900">{unit.holder.position_title}</p>
                                                                    <p className="text-gray-600">{unit.holder.academic_degree} {unit.holder.first_name} {unit.holder.last_name}</p>
                                                                </div>
                                                            ) : (
                                                                <div className="mt-2 text-sm text-gray-500 italic">Sin jefe operativo asignado</div>
                                                            )}
                                                        </div>
                                                        <button
                                                            onClick={() => openModal(unit.id, 'AdministrativeUnit', unit.name, unit.holder)}
                                                            className="text-sm text-indigo-600 hover:text-indigo-900 font-semibold opacity-0 group-hover:opacity-100 transition-opacity focus:opacity-100"
                                                        >
                                                            Editar Titular
                                                        </button>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <Modal show={isModalOpen} onClose={closeModal} maxWidth="lg">
                <div className="p-6 bg-white rounded-lg">
                    <h2 className="text-xl font-bold text-gray-900 mb-2">
                        Asignar Titular
                    </h2>
                    <p className="text-sm text-gray-500 mb-6 pb-4 border-b border-gray-200">
                        Para: <span className="font-semibold text-gray-800">{modalContext?.title}</span>
                    </p>
                    
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <InputLabel htmlFor="academic_degree" value="Grado Académico (Ej. Mtro. en D., Ing., C.)" />
                            <TextInput
                                id="academic_degree"
                                type="text"
                                className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                value={data.academic_degree}
                                onChange={(e) => setData('academic_degree', e.target.value)}
                            />
                            <InputError message={errors.academic_degree} className="mt-2" />
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel htmlFor="first_name" value="Nombre(s)" />
                                <TextInput
                                    id="first_name"
                                    type="text"
                                    className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    value={data.first_name}
                                    onChange={(e) => setData('first_name', e.target.value)}
                                    required
                                />
                                <InputError message={errors.first_name} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="last_name" value="Apellidos" />
                                <TextInput
                                    id="last_name"
                                    type="text"
                                    className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    value={data.last_name}
                                    onChange={(e) => setData('last_name', e.target.value)}
                                    required
                                />
                                <InputError message={errors.last_name} className="mt-2" />
                            </div>
                        </div>

                        <div>
                            <InputLabel htmlFor="position_title" value="Cargo Oficial (Ej. Director General)" />
                            <TextInput
                                id="position_title"
                                type="text"
                                className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                value={data.position_title}
                                onChange={(e) => setData('position_title', e.target.value)}
                                required
                            />
                            <InputError message={errors.position_title} className="mt-2" />
                        </div>

                        <div className="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                            <SecondaryButton onClick={closeModal} disabled={processing}>
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton disabled={processing} className="bg-indigo-600 hover:bg-indigo-700">
                                Guardar Titular
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
