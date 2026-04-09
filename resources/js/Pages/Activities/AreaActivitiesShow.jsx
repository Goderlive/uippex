import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function AreaActivitiesShow({ unit, activities, current_month, can_validate }) {
    const months = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedActivity, setSelectedActivity] = useState(null);

    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        reported_value: '',
        evidence: null,
        month: current_month,
        observations: '',
    });

    const openReportModal = (activity) => {
        setSelectedActivity(activity);
        setData({
            reported_value: activity.current_report ? activity.current_report.reported_value : '',
            evidence: null,
            month: current_month,
            observations: activity.current_report ? (activity.current_report.observations || '') : '',
        });
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setSelectedActivity(null);
        reset();
        clearErrors();
    };

    const submitEnlace = (e) => {
        e.preventDefault();
        post(route('activities.progress.store', selectedActivity.id), {
            onSuccess: () => closeModal(),
            forceFormData: true,
        });
    };

    const submitAdmin = (action) => {
        router.post(route('activities.progress.validate', selectedActivity.current_report.id), {
            action: action,
            observations: data.observations,
        }, {
            onSuccess: () => closeModal(),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span className="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-1">
                            {can_validate ? 'AUDITORÍA PBR' : 'CAPTURA DE AVANCES'} | Unidad: {unit.name}
                        </span>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                            {can_validate ? 'Validación de Metas Mensuales' : 'Seguimiento de Actividades'}
                        </h2>
                    </div>
                </div>
            }
        >
            <Head title={`Seguimiento - ${unit.name}`} />

            <div className="py-6">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    {/* Month Selector Tabs */}
                    <div className="bg-white dark:bg-gray-800 p-2 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 overflow-x-auto">
                        <div className="flex space-x-1 min-w-max">
                            {months.map((name, index) => {
                                const m = index + 1;
                                const isActive = current_month === m;
                                return (
                                    <Link
                                        key={m}
                                        href={route('activities.area.show', unit.id)}
                                        data={{ month: m }}
                                        preserveScroll
                                        className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                                            isActive 
                                                ? 'bg-indigo-600 text-white shadow-md' 
                                                : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400'
                                        }`}
                                    >
                                        {name}
                                    </Link>
                                );
                            })}
                        </div>
                    </div>

                    <div className="mb-6 flex items-center justify-between">
                        <h3 className="text-lg font-bold text-gray-700 dark:text-gray-300">
                            {can_validate ? 'Registros de ' : 'Actividades para '} <span className="text-indigo-600 dark:text-indigo-400">{months[current_month - 1]}</span>
                        </h3>
                        <Link 
                            href={can_validate ? route('activities.department.show', unit.department_id) : route('activities.index')}
                            className="text-sm text-gray-500 hover:text-indigo-600 flex items-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver
                        </Link>
                    </div>

                    {/* Activity Cards List */}
                    <div className="space-y-4">
                        {activities.map((activity) => {
                            const report = activity.current_report;
                            let statusColor = 'bg-blue-600';
                            let statusText = can_validate ? 'Sin Reporte' : 'Reportar Avance';
                            
                            if (report) {
                                if (report.status === 0) {
                                    statusColor = 'bg-yellow-500';
                                    statusText = can_validate ? 'Auditar Registro' : 'En Revisión (Pendiente)';
                                } else if (report.status === 1) {
                                    statusColor = 'bg-green-600';
                                    statusText = `Validado [ ${report.reported_value} ]`;
                                }
                            }

                            return (
                                <div key={activity.id} className="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                    <div className="p-6">
                                        <div className="flex flex-col lg:flex-row justify-between gap-6">
                                            <div className="flex-1">
                                                <h4 className="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug">
                                                    {activity.name}
                                                </h4>
                                                <div className="flex flex-wrap gap-4 text-xs">
                                                    <span className="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 rounded border border-gray-200 dark:border-gray-700">
                                                        Meta Anual: <span className="font-bold text-gray-900 dark:text-gray-200">{activity.annual_target}</span>
                                                    </span>
                                                    <span className="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 rounded border border-indigo-100 dark:border-indigo-800/30">
                                                        Programado {months[current_month-1]}: <span className="font-bold">{activity.month_target}</span>
                                                    </span>
                                                    <span className="px-2 py-1 bg-gray-50 dark:bg-gray-900 text-gray-500 rounded italic">
                                                        Unidad: {activity.measurement_unit}
                                                    </span>
                                                </div>
                                                {report?.observations && (
                                                    <div className="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded text-xs text-red-700 dark:text-red-300">
                                                        <strong>Obs:</strong> {report.observations}
                                                    </div>
                                                )}
                                            </div>
                                            
                                            <div className="flex flex-col justify-center min-w-[200px]">
                                                <button
                                                    onClick={() => openReportModal(activity)}
                                                    disabled={can_validate && !report}
                                                    className={`w-full py-3 px-4 rounded-lg text-white font-bold text-sm shadow-sm transition-all hover:scale-[1.02] active:scale-[0.98] ${statusColor} ${(can_validate && !report) ? 'opacity-50 cursor-not-allowed' : ''}`}
                                                >
                                                    {statusText}
                                                </button>
                                                {report && (
                                                    <p className="mt-2 text-[10px] text-center text-gray-400 uppercase tracking-tighter">
                                                        {report.status === 1 ? 'CERRADO POR SISTEMA' : 'ESPERANDO ACCIÓN'}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="h-1 bg-gray-100 dark:bg-gray-700 w-full overflow-hidden">
                                        <div 
                                            className={`h-full transition-all duration-1000 ${report ? (report.status === 1 ? 'bg-green-500' : 'bg-yellow-500') : 'bg-gray-300'}`}
                                            style={{ width: report ? `${Math.min((report.reported_value / (activity.month_target || 1)) * 100, 100)}%` : '0%' }}
                                        ></div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    {activities.length === 0 && (
                        <div className="text-center py-20 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                            <p className="text-gray-500">No hay actividades asignadas a esta unidad administrativa.</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Reporting / Audit Modal */}
            <Modal show={isModalOpen} onClose={closeModal} maxWidth="2xl">
                <div className="p-6">
                    <div className="flex items-center justify-between mb-6">
                        <h2 className="text-lg font-bold text-gray-900 dark:text-white">
                            {can_validate ? 'Auditoría de Avance' : 'Reportar Avance'} <span className="text-indigo-600">({months[current_month-1]})</span>
                        </h2>
                        <button type="button" onClick={closeModal} className="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-6 bg-gray-50 dark:bg-gray-900/40 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                        <span className="font-bold">Actividad:</span> {selectedActivity?.name}
                    </p>

                    {!can_validate ? (
                        /* ENLACE UI: FORMULARIO DE CAPTURA */
                        <form onSubmit={submitEnlace} className="space-y-6">
                            {(selectedActivity?.current_report?.status === 1) ? (
                                <div className="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 p-4 rounded-lg text-green-800 dark:text-green-300 text-sm">
                                    <p className="font-bold flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                        </svg>
                                        Registro Validado y Cerrado
                                    </p>
                                    <p className="mt-1 opacity-80 uppercase text-[10px]">Cualquier modificación requiere reapertura del Administrador.</p>
                                </div>
                            ) : null}

                            <div className={selectedActivity?.current_report?.status === 1 ? 'opacity-60 pointer-events-none' : ''}>
                                <div>
                                    <InputLabel htmlFor="reported_value" value={`Valor alcanzado en ${months[current_month-1]}`} />
                                    <TextInput
                                        id="reported_value"
                                        type="number"
                                        className="mt-1 block w-full"
                                        value={data.reported_value}
                                        onChange={(e) => setData('reported_value', e.target.value)}
                                        placeholder={`Meta mensual: ${selectedActivity?.month_target}`}
                                        required
                                        disabled={selectedActivity?.current_report?.status === 1}
                                    />
                                    <InputError message={errors.reported_value} className="mt-2" />
                                </div>

                                <div className="mt-6">
                                    <InputLabel htmlFor="evidence" value="Actualizar Evidencia (Imagen/PDF)" />
                                    <input
                                        id="evidence"
                                        type="file"
                                        accept="image/*,.pdf"
                                        className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-700 dark:file:text-gray-300"
                                        onChange={(e) => setData('evidence', e.target.files[0])}
                                        disabled={selectedActivity?.current_report?.status === 1}
                                    />
                                    <InputError message={errors.evidence} className="mt-2" />
                                </div>

                                <div className="flex items-center justify-end mt-8">
                                    <SecondaryButton onClick={closeModal} className="mr-3">
                                        Cancelar
                                    </SecondaryButton>
                                    <PrimaryButton disabled={processing || selectedActivity?.current_report?.status === 1}>
                                        {selectedActivity?.current_report ? 'Actualizar Reporte' : 'Enviar Avance'}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    ) : (
                        /* ADMIN UI: AUDITORÍA Y VALIDACIÓN */
                        <div className="space-y-8">
                            <div className="grid grid-cols-2 gap-6 bg-indigo-50/30 dark:bg-indigo-900/10 p-4 rounded-xl border border-indigo-100/50 dark:border-indigo-800/30">
                                <div className="text-center border-r border-indigo-100 dark:border-indigo-800">
                                    <p className="text-xs text-indigo-600 dark:text-indigo-400 uppercase font-bold mb-1">Valor Programado</p>
                                    <p className="text-3xl font-black text-gray-900 dark:text-white">{selectedActivity?.month_target}</p>
                                </div>
                                <div className="text-center">
                                    <p className="text-xs text-indigo-600 dark:text-indigo-400 uppercase font-bold mb-1">Valor Reportado</p>
                                    <p className="text-3xl font-black text-indigo-700 dark:text-indigo-300">{selectedActivity?.current_report?.reported_value}</p>
                                </div>
                            </div>

                            {selectedActivity?.current_report?.evidence_url ? (
                                <div className="bg-gray-50 dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                                    <p className="text-xs font-bold text-gray-500 uppercase mb-3">Evidencia Adjunta:</p>
                                    <div className="relative group overflow-hidden rounded-lg bg-white dark:bg-gray-800 h-48 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                        {selectedActivity.current_report.evidence_path.toLowerCase().endsWith('.pdf') ? (
                                            <div className="text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-12 w-12 text-red-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.293 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <p className="text-sm font-medium">Documento PDF</p>
                                            </div>
                                        ) : (
                                            <img 
                                                src={selectedActivity.current_report.evidence_url} 
                                                alt="Evidencia" 
                                                className="max-h-full object-contain transition-transform group-hover:scale-105"
                                            />
                                        )}
                                        <a 
                                            href={selectedActivity.current_report.evidence_url} 
                                            target="_blank" 
                                            rel="noopener noreferrer"
                                            className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white font-bold text-sm"
                                        >
                                            Ver en Pantalla Completa ↗
                                        </a>
                                    </div>
                                </div>
                            ) : (
                                <p className="text-sm italic text-gray-500 text-center py-4">No se adjuntó evidencia física.</p>
                            )}

                            <div>
                                <InputLabel htmlFor="observations" value="Observaciones de Auditoría" />
                                <textarea
                                    id="observations"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    rows="3"
                                    value={data.observations}
                                    onChange={(e) => setData('observations', e.target.value)}
                                    placeholder="Indique si hay errores o comentarios para el enlace..."
                                ></textarea>
                                <InputError message={errors.observations} className="mt-2" />
                            </div>

                            <div className="flex items-center justify-between border-t border-gray-100 dark:border-gray-800 pt-6">
                                <SecondaryButton onClick={closeModal}>Cerrar</SecondaryButton>
                                
                                <div className="flex space-x-3">
                                    <button
                                        type="button"
                                        onClick={() => submitAdmin('reject')}
                                        disabled={processing}
                                        className="inline-flex items-center px-6 py-2 bg-red-100 border border-transparent rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest hover:bg-red-200 active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition-all"
                                    >
                                        ❌ Rechazar Avance
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => submitAdmin('approve')}
                                        disabled={processing}
                                        className="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-all"
                                    >
                                        ✅ Validar y Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
