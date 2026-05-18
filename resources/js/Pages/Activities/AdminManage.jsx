import React, { useState, useEffect, useRef } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm } from '@inertiajs/react';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

const MONTH_LABELS = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
const MONTH_KEYS = [
    'jan_programmed', 'feb_programmed', 'mar_programmed', 'apr_programmed',
    'may_programmed', 'jun_programmed', 'jul_programmed', 'aug_programmed',
    'sep_programmed', 'oct_programmed', 'nov_programmed', 'dec_programmed',
];

export default function AdminManage({ auth, activities, filters, flash }) {
    // --- Search debounced ---
    const [search, setSearch] = useState(filters.search || '');
    const debounceRef = useRef(null);

    useEffect(() => {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('activities.manage.index'), { search: search || undefined }, {
                preserveState: true,
                replace: true,
            });
        }, 400);
        return () => clearTimeout(debounceRef.current);
    }, [search]);

    // --- Modal state ---
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingActivity, setEditingActivity] = useState(null);

    const { data, setData, put, processing, errors, reset, clearErrors } = useForm({
        jan_programmed: 0, feb_programmed: 0, mar_programmed: 0, apr_programmed: 0,
        may_programmed: 0, jun_programmed: 0, jul_programmed: 0, aug_programmed: 0,
        sep_programmed: 0, oct_programmed: 0, nov_programmed: 0, dec_programmed: 0,
    });

    const openEditor = (activity) => {
        setEditingActivity(activity);
        const schedule = activity.monthly_schedule;
        const initial = {};
        MONTH_KEYS.forEach(k => { initial[k] = schedule ? schedule[k] ?? 0 : 0; });
        setData(initial);
        clearErrors();
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingActivity(null);
        reset();
    };

    const submit = (e) => {
        e.preventDefault();
        if (!editingActivity?.monthly_schedule?.id) return;
        put(route('activities.manage.update', editingActivity.monthly_schedule.id), {
            onSuccess: () => closeModal(),
        });
    };

    const annualTotal = MONTH_KEYS.reduce((sum, k) => sum + (parseFloat(data[k]) || 0), 0);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Administrar Programaciones</h2>}
        >
            <Head title="Administrar Programaciones" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {flash?.message && (
                        <div className="mb-4 font-medium text-sm text-green-700 bg-green-50 p-4 rounded-lg shadow-sm border border-green-200">
                            {flash.message}
                        </div>
                    )}

                    <div className="bg-white overflow-hidden shadow sm:rounded-lg">
                        {/* Search bar */}
                        <div className="p-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div className="flex items-center gap-2 w-full sm:w-auto">
                                <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Buscar actividad por nombre..."
                                    className="block w-full sm:w-80 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                />
                                {search && (
                                    <button onClick={() => setSearch('')} className="text-gray-400 hover:text-gray-600 text-sm">
                                        Limpiar
                                    </button>
                                )}
                            </div>
                            <p className="text-sm text-gray-500">
                                {activities.total} actividad{activities.total !== 1 ? 'es' : ''} encontrada{activities.total !== 1 ? 's' : ''}
                            </p>
                        </div>

                        {/* Table */}
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dependencia / Área</th>
                                        <th className="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actividad</th>
                                        <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Meta Anual</th>
                                        <th className="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {activities.data.length === 0 ? (
                                        <tr>
                                            <td colSpan={4} className="px-4 py-8 text-center text-gray-500">
                                                No se encontraron actividades{search ? ` para "${search}"` : ''}.
                                            </td>
                                        </tr>
                                    ) : (
                                        activities.data.map((activity) => (
                                            <tr key={activity.id} className="hover:bg-gray-50 transition-colors">
                                                <td className="px-4 py-3 text-sm">
                                                    <div className="font-semibold text-gray-900">{activity.administrative_unit?.department?.name ?? '—'}</div>
                                                    <div className="text-xs text-gray-500">{activity.administrative_unit?.name ?? '—'}</div>
                                                </td>
                                                <td className="px-4 py-3 text-sm text-gray-800 max-w-xs truncate" title={activity.name}>
                                                    {activity.name}
                                                </td>
                                                <td className="px-4 py-3 text-sm text-center font-mono">
                                                    {activity.annual_target ?? 0}
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    {activity.monthly_schedule ? (
                                                        <button
                                                            onClick={() => openEditor(activity)}
                                                            className="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                                        >
                                                            Editar Matriz
                                                        </button>
                                                    ) : (
                                                        <span className="text-xs text-amber-600 italic">Sin programación</span>
                                                    )}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {activities.links && activities.links.length > 3 && (
                            <div className="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                                <p className="text-sm text-gray-600">
                                    Página {activities.current_page} de {activities.last_page}
                                </p>
                                <div className="flex gap-1 flex-wrap">
                                    {activities.links.map((link, i) => (
                                        <button
                                            key={i}
                                            disabled={!link.url}
                                            onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                                            className={`px-3 py-1 text-sm rounded border transition-colors ${
                                                link.active
                                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                                    : link.url
                                                        ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
                                                        : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                            }`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Matrix Editor Modal */}
            <Modal show={isModalOpen} onClose={closeModal} maxWidth="2xl">
                <div className="p-6 bg-white rounded-lg">
                    <h2 className="text-xl font-bold text-gray-900 mb-1">
                        Editor Matricial de Programación
                    </h2>
                    <p className="text-sm text-gray-500 mb-6 pb-4 border-b border-gray-200">
                        Actividad: <span className="font-semibold text-gray-800">{editingActivity?.name}</span>
                    </p>

                    <form onSubmit={submit}>
                        <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                            {MONTH_KEYS.map((key, idx) => (
                                <div key={key}>
                                    <InputLabel htmlFor={key} value={MONTH_LABELS[idx]} className="text-center text-xs font-bold uppercase tracking-wider text-gray-500" />
                                    <TextInput
                                        id={key}
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        className="mt-1 block w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                        value={data[key]}
                                        onChange={(e) => setData(key, parseFloat(e.target.value) || 0)}
                                    />
                                    <InputError message={errors[key]} className="mt-1" />
                                </div>
                            ))}
                        </div>

                        <div className="mt-4 pt-3 border-t border-gray-100 text-center">
                            <span className="text-sm text-gray-500">Meta Anual Resultante: </span>
                            <span className="text-lg font-bold text-indigo-700">{annualTotal.toFixed(2)}</span>
                        </div>

                        <div className="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                            <SecondaryButton onClick={closeModal} disabled={processing}>
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton disabled={processing} className="bg-indigo-600 hover:bg-indigo-700">
                                Guardar Programación
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
