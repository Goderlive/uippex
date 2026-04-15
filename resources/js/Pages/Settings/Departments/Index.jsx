import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, router } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import DangerButton from '@/Components/DangerButton';
import Modal from '@/Components/Modal';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';

export default function DepartmentsIndex({ auth, departments, allDepartments, flash }) {
    const [isCreateModalOpen, setCreateModalOpen] = useState(false);
    
    // Modal states for Departments
    const [editDeptId, setEditDeptId] = useState(null);
    const [editDeptName, setEditDeptName] = useState('');
    
    const [deleteDeptId, setDeleteDeptId] = useState(null);

    // Modal states for Areas
    const [editAreaId, setEditAreaId] = useState(null);
    const [editAreaName, setEditAreaName] = useState('');

    const [moveAreaId, setMoveAreaId] = useState(null);
    const [targetDeptId, setTargetDeptId] = useState('');

    const { data: createData, setData: setCreateData, post: createPost, processing: createProcessing, errors: createErrors, reset: createReset } = useForm({
        name: '',
    });

    const handleCreateDepartment = (e) => {
        e.preventDefault();
        createPost(route('departments.store'), {
            onSuccess: () => {
                setCreateModalOpen(false);
                createReset();
            },
        });
    };

    const submitEditDept = (e) => {
        e.preventDefault();
        router.put(route('departments.update', editDeptId), { name: editDeptName }, {
            onSuccess: () => setEditDeptId(null)
        });
    };

    const submitDeleteDept = () => {
        router.delete(route('departments.destroy', deleteDeptId), {
            onSuccess: () => setDeleteDeptId(null)
        });
    };

    const submitEditArea = (e) => {
        e.preventDefault();
        router.put(route('areas.update', editAreaId), { name: editAreaName }, {
            onSuccess: () => setEditAreaId(null)
        });
    };

    const submitMoveArea = (e) => {
        e.preventDefault();
        router.post(route('areas.move', moveAreaId), { department_id: targetDeptId }, {
            onSuccess: () => setMoveAreaId(null)
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Ajustes de Dependencias
                    </h2>
                    <button
                        onClick={() => setCreateModalOpen(true)}
                        className="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-5 rounded-lg shadow-md transition duration-150 ease-in-out"
                    >
                        + Nueva Dependencia
                    </button>
                </div>
            }
        >
            <Head title="Dependencias" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {flash?.message && (
                        <div className="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800 shadow-sm animate-pulse">
                            {flash.message}
                        </div>
                    )}
                    {flash?.error && (
                        <div className="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800 shadow-sm">
                            {flash.error}
                        </div>
                    )}

                    <div className="grid gap-6">
                        {departments.map((dept) => (
                            <div key={dept.id} className="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group">
                                <div className="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                                    <div className="flex items-center space-x-3">
                                        <div className="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-lg shadow-inner">
                                            {dept.name.charAt(0).toUpperCase()}
                                        </div>
                                        <h3 className="text-xl font-medium text-gray-900 dark:text-gray-100">{dept.name}</h3>
                                        <span className="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {dept.administrative_units?.length || 0} Áreas
                                        </span>
                                    </div>
                                    <div className="flex space-x-2">
                                        <button
                                            onClick={() => { setEditDeptId(dept.id); setEditDeptName(dept.name); }}
                                            className="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                                            title="Editar Nombre de Dependencia"
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button
                                            onClick={() => setDeleteDeptId(dept.id)}
                                            disabled={dept.administrative_units?.length > 0}
                                            className={`transition ${dept.administrative_units?.length > 0 ? 'text-gray-300 dark:text-gray-600 cursor-not-allowed' : 'text-gray-400 hover:text-red-600 dark:hover:text-red-400'}`}
                                            title={dept.administrative_units?.length > 0 ? 'No se puede eliminar porque contiene áreas' : 'Eliminar Dependencia'}
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                <div className="p-6">
                                    {(dept.administrative_units && dept.administrative_units.length > 0) ? (
                                        <ul className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {dept.administrative_units.map(area => (
                                                <li key={area.id} className="flex justify-between items-center p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition duration-150">
                                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300 break-words flex-1 pr-4">{area.name}</span>
                                                    <div className="flex items-center space-x-2">
                                                        <button
                                                            onClick={() => { setMoveAreaId(area.id); setTargetDeptId(dept.id); }}
                                                            className="text-xs px-3 py-1.5 rounded bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition font-medium"
                                                        >
                                                            Mover
                                                        </button>
                                                        <button
                                                            onClick={() => { setEditAreaId(area.id); setEditAreaName(area.name); }}
                                                            className="text-xs px-3 py-1.5 rounded bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium"
                                                        >
                                                            Editar
                                                        </button>
                                                    </div>
                                                </li>
                                            ))}
                                        </ul>
                                    ) : (
                                        <div className="text-center py-8">
                                            <p className="text-sm text-gray-500 dark:text-gray-400">Esta dependencia está vacía. No tiene áreas asignadas.</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Create Department Modal */}
            <Modal show={isCreateModalOpen} onClose={() => setCreateModalOpen(false)}>
                <form onSubmit={handleCreateDepartment} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">Crear Nueva Dependencia</h2>
                    <div className="mt-4">
                        <InputLabel htmlFor="name" value="Nombre de la Dependencia" />
                        <TextInput
                            id="name"
                            className="mt-1 block w-full"
                            value={createData.name}
                            onChange={(e) => setCreateData('name', e.target.value)}
                            isFocused
                        />
                        <InputError message={createErrors.name} className="mt-2" />
                    </div>
                    <div className="mt-6 flex justify-end gap-3">
                        <SecondaryButton onClick={() => setCreateModalOpen(false)}>Cancelar</SecondaryButton>
                        <PrimaryButton disabled={createProcessing}>Crear</PrimaryButton>
                    </div>
                </form>
            </Modal>

            {/* Edit Department Modal */}
            <Modal show={editDeptId !== null} onClose={() => setEditDeptId(null)}>
                <form onSubmit={submitEditDept} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">Editar Nombre de Dependencia</h2>
                    <div className="mt-4">
                        <InputLabel value="Nombre" />
                        <TextInput
                            className="mt-1 block w-full"
                            value={editDeptName}
                            onChange={(e) => setEditDeptName(e.target.value)}
                            isFocused
                        />
                    </div>
                    <div className="mt-6 flex justify-end gap-3">
                        <SecondaryButton type="button" onClick={() => setEditDeptId(null)}>Cancelar</SecondaryButton>
                        <PrimaryButton>Guardar Cambios</PrimaryButton>
                    </div>
                </form>
            </Modal>

            {/* Delete Department Confirmation */}
            <Modal show={deleteDeptId !== null} onClose={() => setDeleteDeptId(null)}>
                <div className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        ¿Estás seguro de que deseas eliminar esta dependencia?
                    </h2>
                    <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Esta acción no se puede deshacer. Sólo puedes eliminar dependencias que no tengan áreas asignadas.
                    </p>
                    <div className="mt-6 flex justify-end gap-3">
                        <SecondaryButton onClick={() => setDeleteDeptId(null)}>Cancelar</SecondaryButton>
                        <DangerButton onClick={submitDeleteDept}>Sí, Eliminar</DangerButton>
                    </div>
                </div>
            </Modal>

            {/* Edit Area Modal */}
            <Modal show={editAreaId !== null} onClose={() => setEditAreaId(null)}>
                <form onSubmit={submitEditArea} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">Editar Nombre del Área</h2>
                    <div className="mt-4">
                        <InputLabel value="Nombre" />
                        <TextInput
                            className="mt-1 block w-full"
                            value={editAreaName}
                            onChange={(e) => setEditAreaName(e.target.value)}
                            isFocused
                        />
                    </div>
                    <div className="mt-6 flex justify-end gap-3">
                        <SecondaryButton type="button" onClick={() => setEditAreaId(null)}>Cancelar</SecondaryButton>
                        <PrimaryButton>Guardar Cambios</PrimaryButton>
                    </div>
                </form>
            </Modal>

            {/* Move Area Modal */}
            <Modal show={moveAreaId !== null} onClose={() => setMoveAreaId(null)}>
                <form onSubmit={submitMoveArea} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">Reasignar Área</h2>
                    <p className="mb-4 text-sm text-gray-600 dark:text-gray-400">Selecciona la dependencia destino para esta área.</p>
                    <div className="mt-4">
                        <InputLabel value="Nueva Dependencia Destino" />
                        <select
                            className="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            value={targetDeptId}
                            onChange={(e) => setTargetDeptId(e.target.value)}
                            required
                        >
                            <option value="">Selecciona una opción</option>
                            {allDepartments.map(dept => (
                                <option key={dept.id} value={dept.id}>{dept.name}</option>
                            ))}
                        </select>
                    </div>
                    <div className="mt-6 flex justify-end gap-3">
                        <SecondaryButton type="button" onClick={() => setMoveAreaId(null)}>Cancelar</SecondaryButton>
                        <PrimaryButton disabled={!targetDeptId}>Reasignar Área</PrimaryButton>
                    </div>
                </form>
            </Modal>
        </AuthenticatedLayout>
    );
}
