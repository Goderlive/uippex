import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, router, Link } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import DangerButton from '@/Components/DangerButton';
import Modal from '@/Components/Modal';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';

export default function ActivitiesIndex({ auth, area, activities, themes, flash }) {
    const [isCreateModalOpen, setCreateModalOpen] = useState(false);
    const [deleteActivityId, setDeleteActivityId] = useState(null);
    const [editingActivityId, setEditingActivityId] = useState(null);

    // Form for creating a new activity
    const { data: createData, setData: setCreateData, post: createPost, processing: createProcessing, errors: createErrors, reset: createReset } = useForm({
        name: '',
        measurement_unit: '',
        development_theme_id: '',
    });

    const handleCreateActivity = (e) => {
        e.preventDefault();
        createPost(route('areas.activities.store', area.id), {
            onSuccess: () => {
                setCreateModalOpen(false);
                createReset();
            },
        });
    };

    const submitDeleteActivity = () => {
        router.delete(route('areas.activities.destroy', { area: area.id, activity: deleteActivityId }), {
            onSuccess: () => setDeleteActivityId(null)
        });
    };

    // State for inline editing
    const [editData, setEditData] = useState({});

    const startEditing = (activity) => {
        setEditingActivityId(activity.id);
        const s = activity.monthly_schedule || {};
        setEditData({
            name: activity.name,
            measurement_unit: activity.measurement_unit,
            development_theme_id: activity.development_theme_id,
            schedule: {
                jan_programmed: parseInt(s.jan_programmed || 0, 10),
                feb_programmed: parseInt(s.feb_programmed || 0, 10),
                mar_programmed: parseInt(s.mar_programmed || 0, 10),
                apr_programmed: parseInt(s.apr_programmed || 0, 10),
                may_programmed: parseInt(s.may_programmed || 0, 10),
                jun_programmed: parseInt(s.jun_programmed || 0, 10),
                jul_programmed: parseInt(s.jul_programmed || 0, 10),
                aug_programmed: parseInt(s.aug_programmed || 0, 10),
                sep_programmed: parseInt(s.sep_programmed || 0, 10),
                oct_programmed: parseInt(s.oct_programmed || 0, 10),
                nov_programmed: parseInt(s.nov_programmed || 0, 10),
                dec_programmed: parseInt(s.dec_programmed || 0, 10),
            }
        });
    };

    const cancelEditing = () => {
        setEditingActivityId(null);
        setEditData({});
    };

    const handleEditChange = (field, value) => {
        setEditData(prev => ({ ...prev, [field]: value }));
    };

    const handleScheduleChange = (month, value) => {
        setEditData(prev => ({
            ...prev,
            schedule: {
                ...prev.schedule,
                [`${month}_programmed`]: value
            }
        }));
    };

    const submitEditActivity = (activityId) => {
        router.put(route('areas.activities.update', { area: area.id, activity: activityId }), editData, {
            onSuccess: () => {
                setEditingActivityId(null);
            },
            preserveScroll: true
        });
    };

    const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
    const monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                            Actividades de: {area.name}
                        </h2>
                        <div className="text-sm text-gray-500 mt-1 mb-1">
                            <span className="font-medium">Sector Gral:</span> {area.general_sector_id || 'N/A'} &nbsp;|&nbsp; 
                            <span className="font-medium">Sector Aux:</span> {area.auxiliary_sector_id || 'N/A'} &nbsp;|&nbsp; 
                            <span className="font-medium">Proyecto:</span> {area.budget_project_id || 'N/A'}
                        </div>
                        <div className="text-sm text-gray-500 mt-1">
                            <Link href={route('departments.index')} className="text-indigo-600 hover:underline">Dependencias</Link>
                            {' / '} {area.department?.name || 'Dependencia'} {' / '} Actividades
                        </div>
                    </div>
                    <button
                        onClick={() => setCreateModalOpen(true)}
                        className="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-5 rounded-lg shadow-md transition duration-150 ease-in-out"
                    >
                        + Nueva Actividad
                    </button>
                </div>
            }
        >
            <Head title={`Actividades - ${area.name}`} />

            <div className="py-12">
                <div className="mx-auto max-w-full sm:px-6 lg:px-8">
                    {flash?.message && (
                        <div className="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800 shadow-sm animate-pulse">
                            {flash.message}
                        </div>
                    )}

                    <div className="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-x-auto border border-gray-100 dark:border-gray-700">
                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead className="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-800 z-10 w-64">
                                        Actividad y Detalles
                                    </th>
                                    {monthLabels.map(m => (
                                        <th key={m} scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">
                                            {m}
                                        </th>
                                    ))}
                                    <th scope="col" className="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th scope="col" className="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider sticky right-0 bg-gray-50 dark:bg-gray-800 z-10">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                {activities.length === 0 ? (
                                    <tr>
                                        <td colSpan={15} className="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No hay actividades registradas para esta área.
                                        </td>
                                    </tr>
                                ) : activities.map((activity) => {
                                    const isEditing = editingActivityId === activity.id;
                                    const s = activity.monthly_schedule || {};
                                    const total = isEditing && editData.schedule
                                        ? months.reduce((acc, m) => acc + (parseInt(editData.schedule[`${m}_programmed`], 10) || 0), 0)
                                        : activity.annual_target;

                                    return (
                                        <tr key={activity.id} className={isEditing ? 'bg-indigo-50/30 dark:bg-indigo-900/10' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50'}>
                                            <td className="px-4 py-4 sticky left-0 bg-white dark:bg-gray-900 z-10 align-top">
                                                {isEditing ? (
                                                    <div className="space-y-2">
                                                        <TextInput 
                                                            className="w-full text-sm py-1" 
                                                            value={editData.name} 
                                                            onChange={e => handleEditChange('name', e.target.value)} 
                                                            placeholder="Nombre de la actividad" 
                                                        />
                                                        <TextInput 
                                                            className="w-full text-sm py-1" 
                                                            value={editData.measurement_unit} 
                                                            onChange={e => handleEditChange('measurement_unit', e.target.value)} 
                                                            placeholder="Unidad de Medida" 
                                                        />
                                                        <select 
                                                            className="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm py-1"
                                                            value={editData.development_theme_id}
                                                            onChange={e => handleEditChange('development_theme_id', e.target.value)}
                                                        >
                                                            <option value="">Seleccionar Tema</option>
                                                            {themes.map(group => (
                                                                <optgroup key={group.axis_name} label={group.axis_name}>
                                                                    {group.themes.map(theme => (
                                                                        <option key={theme.id} value={theme.id}>{theme.name}</option>
                                                                    ))}
                                                                </optgroup>
                                                            ))}
                                                        </select>
                                                    </div>
                                                ) : (
                                                    <div>
                                                        <div className="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">{activity.name}</div>
                                                        <div className="text-xs text-gray-500 dark:text-gray-400">Ud: {activity.measurement_unit}</div>
                                                        <div className="text-xs text-indigo-600 dark:text-indigo-400 mt-1 line-clamp-1" title={activity.theme?.name}>
                                                            Tema: {activity.theme?.name || 'N/A'}
                                                        </div>
                                                    </div>
                                                )}
                                            </td>

                                            {months.map(m => (
                                                <td key={m} className="px-2 py-4 align-top text-center">
                                                    {isEditing ? (
                                                        <input 
                                                            type="number" 
                                                            min="0"
                                                            step="1"
                                                            className="w-16 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs p-1 text-center hide-arrows"
                                                            value={editData.schedule[`${m}_programmed`]}
                                                            onChange={e => handleScheduleChange(m, e.target.value)}
                                                        />
                                                    ) : (
                                                        <span className="text-sm text-gray-700 dark:text-gray-300">
                                                            {s[`${m}_programmed`] || 0}
                                                        </span>
                                                    )}
                                                </td>
                                            ))}

                                            <td className="px-4 py-4 align-top text-center">
                                                <span className="text-sm font-semibold text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                                    {total}
                                                </span>
                                            </td>

                                            <td className="px-4 py-4 sticky right-0 bg-white dark:bg-gray-900 z-10 align-top text-right space-y-2">
                                                {isEditing ? (
                                                    <div className="flex flex-col gap-2">
                                                        <button 
                                                            onClick={() => submitEditActivity(activity.id)}
                                                            className="text-xs px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                                                        >
                                                            Guardar
                                                        </button>
                                                        <button 
                                                            onClick={cancelEditing}
                                                            className="text-xs px-2 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600"
                                                        >
                                                            Cancelar
                                                        </button>
                                                    </div>
                                                ) : (
                                                    <div className="flex flex-col gap-2">
                                                        <button 
                                                            onClick={() => startEditing(activity)}
                                                            className="text-xs px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800"
                                                        >
                                                            Editar
                                                        </button>
                                                        <button 
                                                            onClick={() => setDeleteActivityId(activity.id)}
                                                            className="text-xs px-2 py-1 border border-red-300 dark:border-red-800 rounded text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30"
                                                        >
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                )}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Create Activity Modal */}
            <Modal show={isCreateModalOpen} onClose={() => setCreateModalOpen(false)}>
                <form onSubmit={handleCreateActivity} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Crear Nueva Actividad</h2>
                    
                    <div className="space-y-4">
                        <div>
                            <InputLabel htmlFor="name" value="Nombre de la Actividad" />
                            <TextInput
                                id="name"
                                className="mt-1 block w-full"
                                value={createData.name}
                                onChange={(e) => setCreateData('name', e.target.value)}
                                isFocused
                                required
                            />
                            <InputError message={createErrors.name} className="mt-2" />
                        </div>

                        <div>
                            <InputLabel htmlFor="measurement_unit" value="Unidad de Medida" />
                            <TextInput
                                id="measurement_unit"
                                className="mt-1 block w-full"
                                value={createData.measurement_unit}
                                onChange={(e) => setCreateData('measurement_unit', e.target.value)}
                                required
                            />
                            <InputError message={createErrors.measurement_unit} className="mt-2" />
                        </div>

                        <div>
                            <InputLabel htmlFor="development_theme_id" value="Tema de Desarrollo" />
                            <select
                                id="development_theme_id"
                                className="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                value={createData.development_theme_id}
                                onChange={(e) => setCreateData('development_theme_id', e.target.value)}
                                required
                            >
                                <option value="">Seleccione un tema</option>
                                {themes.map(group => (
                                    <optgroup key={group.axis_name} label={group.axis_name}>
                                        {group.themes.map(theme => (
                                            <option key={theme.id} value={theme.id}>{theme.name}</option>
                                        ))}
                                    </optgroup>
                                ))}
                            </select>
                            <InputError message={createErrors.development_theme_id} className="mt-2" />
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end gap-3">
                        <SecondaryButton type="button" onClick={() => setCreateModalOpen(false)}>Cancelar</SecondaryButton>
                        <PrimaryButton disabled={createProcessing}>Crear Actividad</PrimaryButton>
                    </div>
                </form>
            </Modal>

            {/* Delete Confirmation */}
            <Modal show={deleteActivityId !== null} onClose={() => setDeleteActivityId(null)}>
                <div className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        ¿Estás seguro de que deseas eliminar esta actividad?
                    </h2>
                    <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Esta acción removerá la actividad y su programación. Se recomienda usar la opción de reconducción si ya tiene reportes de avance.
                    </p>
                    <div className="mt-6 flex justify-end gap-3">
                        <SecondaryButton onClick={() => setDeleteActivityId(null)}>Cancelar</SecondaryButton>
                        <DangerButton onClick={submitDeleteActivity}>Sí, Eliminar</DangerButton>
                    </div>
                </div>
            </Modal>
            
            <style jsx>{`
                /* Hide number input arrows */
                .hide-arrows::-webkit-outer-spin-button,
                .hide-arrows::-webkit-inner-spin-button {
                    -webkit-appearance: none;
                    margin: 0;
                }
                .hide-arrows[type=number] {
                    -moz-appearance: textfield;
                }
            `}</style>
        </AuthenticatedLayout>
    );
}
