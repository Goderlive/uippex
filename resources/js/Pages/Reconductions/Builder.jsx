import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';

export default function Builder({ reconduction, available_activities, can_edit, can_validate }) {
    // Array Draft of modifications (The Matrix Builder)
    const [draftItems, setDraftItems] = useState([]);
    const [selectedActivityId, setSelectedActivityId] = useState('');

    useEffect(() => {
        // Hydrate draft items from server if they exist in the Reconduction eager load
        if (reconduction.items && reconduction.items.length > 0) {
            setDraftItems(reconduction.items);
        }
    }, [reconduction]);

    const handleAddActivity = () => {
        if (!selectedActivityId) return;

        // Prevent duplicates
        if (draftItems.some(i => i.substantive_activity_id === parseInt(selectedActivityId))) {
            alert("Esta actividad ya está agregada al dictamen actual.");
            return;
        }

        const act = available_activities.find(a => a.id === parseInt(selectedActivityId));
        if (!act) return;

        // If activity has no schedule, it's a structural error but we default to zeros
        const initialSchedule = act.schedule_matrix || {
            jan: 0, feb: 0, mar: 0, apr: 0, may: 0, jun: 0, jul: 0, aug: 0, sep: 0, oct: 0, nov: 0, dec: 0
        };

        const newItem = {
            id: `temp_${Date.now()}`, // Temporary ID for React lists
            substantive_activity_id: act.id,
            activity_name: act.name, // Local display prop
            modification_type: 'increase',
            previous_annual_goal: act.current_annual_goal || 0,
            new_annual_goal: act.current_annual_goal || 0,
            achieved_so_far: act.achieved_so_far || 0,
            previous_schedule: { ...initialSchedule }, // JSON Snapshot
            new_schedule: { ...initialSchedule }, // Deep copy for editable state
            justification: ''
        };

        setDraftItems([...draftItems, newItem]);
        setSelectedActivityId('');
    };

    const handleRemoveItem = (indexToRemove) => {
        setDraftItems(draftItems.filter((_, i) => i !== indexToRemove));
    };

    const handleScheduleChange = (itemIndex, monthKey, value) => {
        const valStr = value === '' ? '0' : value;
        const numValue = parseFloat(valStr) || 0;
        
        const newItems = [...draftItems];
        const item = newItems[itemIndex];
        
        // Update specific month in new_schedule
        item.new_schedule[monthKey] = numValue;
        
        // Auto-recalculate new_annual_goal dynamically
        item.new_annual_goal = Object.values(item.new_schedule).reduce((acc, curr) => acc + curr, 0);

        setDraftItems(newItems);
    };

    const handleItemChange = (itemIndex, field, value) => {
        const newItems = [...draftItems];
        newItems[itemIndex][field] = value;
        setDraftItems(newItems);
    };

    const saveDraftDetails = () => {
        router.put(route('reconductions.update', reconduction.id), { items: draftItems }, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    const submitReconduction = () => {
        if(!confirm("¿Deseas enviar este dictamen a revisión? Ya no podrás editarlo.")) return;
        router.post(route('reconductions.submit', reconduction.id));
    };

    const approveReconduction = () => {
        if(!confirm("⚠️ APROBACIÓN OSFEM: Esto reescribirá instantáneamente las metas en el PBR oficial. ¿Estás seguro?")) return;
        router.post(route('reconductions.approve', reconduction.id));
    };

    const monthNames = [
        {k:'jan', n:'Ene'}, {k:'feb', n:'Feb'}, {k:'mar', n:'Mar'},
        {k:'apr', n:'Abr'}, {k:'may', n:'May'}, {k:'jun', n:'Jun'},
        {k:'jul', n:'Jul'}, {k:'aug', n:'Ago'}, {k:'sep', n:'Sep'},
        {k:'oct', n:'Oct'}, {k:'nov', n:'Nov'}, {k:'dec', n:'Dic'},
    ];

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <span className="text-xs font-semibold uppercase tracking-wider text-rose-500 mb-1">
                            {reconduction.document_number} · Trimestre {reconduction.quarter}
                        </span>
                        <h2 className="text-xl font-bold text-gray-800 dark:text-gray-200 uppercase">
                            Dictamen de Reconducción Programática
                        </h2>
                    </div>
                    <div>
                        <span className={`px-3 py-1 text-sm rounded-full font-bold
                            ${reconduction.status === 0 ? 'bg-gray-100 text-gray-700' :
                              reconduction.status === 1 ? 'bg-orange-100 text-orange-700 border border-orange-200' :
                              'bg-green-100 text-green-700'}`}>
                            ESTADO: {reconduction.status === 0 ? 'EN BORRADOR' : reconduction.status === 1 ? 'REVISIÓN PÚBLICA' : 'APLICADO EN SISTEMA'}
                        </span>
                    </div>
                </div>
            }
        >
            <Head title="Constructor PBR" />

            <div className="mx-auto max-w-7xl sm:px-6 lg:px-8 py-8 space-y-6">

                {/* AREA OF SELECTOR (ONLY for ENLACES in DRAFT MODE) */}
                {can_edit && (
                    <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                        <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Añadir Actividad a Modificar</label>
                        <div className="flex space-x-4">
                            <select 
                                className="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:bg-gray-900 dark:border-gray-700 text-sm"
                                value={selectedActivityId}
                                onChange={(e) => setSelectedActivityId(e.target.value)}
                            >
                                <option value="">-- Seleccionar Actividad Sustantiva --</option>
                                {available_activities.map(act => (
                                    <option key={act.id} value={act.id}>
                                        {act.name} (Meta Actual: {act.current_annual_goal} | Alcanzado: {act.achieved_so_far})
                                    </option>
                                ))}
                            </select>
                            <PrimaryButton type="button" onClick={handleAddActivity} className="bg-rose-600 hover:bg-rose-700 focus:bg-rose-700">
                                ➕ Añadir al Dictamen
                            </PrimaryButton>
                        </div>
                    </div>
                )}

                {/* THE MATRIX BUILDER ITERATOR */}
                <div className="space-y-6">
                    {draftItems.map((item, index) => {
                        // Rescatar nombre de actividad si no fue recién añadida y viene de DB
                        const displayActivityName = item.activity_name || available_activities.find(a=>a.id === item.substantive_activity_id)?.name || 'Actividad Desconocida';

                        return (
                            <div key={item.id || index} className="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div className="bg-gray-50 dark:bg-gray-900/50 p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                    <h3 className="font-bold text-gray-800 dark:text-gray-100 text-sm">{displayActivityName}</h3>
                                    {can_edit && (
                                        <button onClick={() => handleRemoveItem(index)} className="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50/50 px-2 py-1 rounded">
                                            QUITAR 🗑
                                        </button>
                                    )}
                                </div>
                                
                                <div className="p-6">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                                        <div>
                                            <InputLabel value="Tipo de Movimiento OSFEM" />
                                            <select 
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:bg-gray-900 dark:border-gray-700 text-sm"
                                                value={item.modification_type}
                                                onChange={(e) => handleItemChange(index, 'modification_type', e.target.value)}
                                                disabled={!can_edit}
                                            >
                                                <option value="increase">Incremento / Ampliación</option>
                                                <option value="reduction">Reducción / Disminución</option>
                                                <option value="cancellation">Cancelación Definitiva</option>
                                                <option value="creation">Creación Extraordinaria</option>
                                            </select>
                                        </div>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div className="bg-indigo-50 dark:bg-indigo-900/20 p-2 text-center rounded">
                                                <p className="text-[10px] text-indigo-800 font-bold uppercase mb-1">Total Anterior</p>
                                                <p className="text-xl font-black">{item.previous_annual_goal}</p>
                                            </div>
                                            <div className="bg-rose-50 dark:bg-rose-900/20 p-2 text-center rounded">
                                                <p className="text-[10px] text-rose-800 font-bold uppercase mb-1">La Nueva Meta PBR</p>
                                                <p className="text-xl font-black">{item.new_annual_goal}</p>
                                                <p className="text-[9px] text-gray-500 italic mt-1">Avance Real Actual: {item.achieved_so_far}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {/* HORIZONTAL MONTHS MATRIX */}
                                    <div className="space-y-2 mb-6 border border-gray-100 dark:border-gray-800 rounded-lg p-4 bg-gray-50/50 dark:bg-gray-900/10">
                                        <p className="text-xs font-bold uppercase text-gray-500 mb-2">Reconfiguración de la Matriz Calendario PBR</p>
                                        <div className="overflow-auto pb-2">
                                            <table className="min-w-full text-center divide-y divide-gray-200 dark:divide-gray-800">
                                                <thead>
                                                    <tr>
                                                        <th className="py-2 text-xs font-bold text-gray-400">Ver.</th>
                                                        {monthNames.map(m => (
                                                            <th key={m.k} className="py-2 text-xs font-bold text-gray-600 dark:text-gray-400 border-x border-gray-200/50">{m.n}</th>
                                                        ))}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr className="bg-white dark:bg-gray-800">
                                                        <td className="text-[10px] font-bold text-gray-400 py-2">ORIG.</td>
                                                        {monthNames.map(m => (
                                                            <td key={m.k} className="text-xs text-gray-500 p-1 border-x border-gray-100 dark:border-gray-800">
                                                                {item.previous_schedule[m.k]}
                                                            </td>
                                                        ))}
                                                    </tr>
                                                    <tr className="bg-rose-50/30 dark:bg-rose-900/10">
                                                        <td className="text-[10px] font-bold text-rose-500 py-3">NUEVA</td>
                                                        {monthNames.map(m => (
                                                            <td key={m.k} className="px-1 border-x border-rose-100 dark:border-gray-800 align-middle">
                                                                <input 
                                                                    type="number" min="0" step="0.01" 
                                                                    className="w-16 mx-auto text-xs p-1 text-center font-bold text-indigo-700 bg-white border border-rose-200 rounded focus:ring focus:ring-rose-200 dark:bg-gray-900 dark:text-indigo-300 dark:border-rose-900"
                                                                    value={item.new_schedule[m.k]}
                                                                    onChange={(e) => handleScheduleChange(index, m.k, e.target.value)}
                                                                    disabled={!can_edit}
                                                                />
                                                            </td>
                                                        ))}
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {/* Justification Textarea */}
                                    <div>
                                        <InputLabel value="Justificación Normativa OSFEM" />
                                        <textarea
                                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-rose-500 focus:ring-rose-500 dark:bg-gray-900 dark:border-gray-700 text-sm"
                                            rows="2"
                                            value={item.justification}
                                            onChange={(e) => handleItemChange(index, 'justification', e.target.value)}
                                            placeholder="Describa el fundamento legal y técnico de la reconducción..."
                                            disabled={!can_edit}
                                        ></textarea>
                                    </div>

                                </div>
                            </div>
                        );
                    })}

                    {draftItems.length === 0 && (
                        <div className="text-center p-12 bg-white dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 text-gray-400">
                            Aún no hay actividades seleccionadas para reformar en este dictamen.
                        </div>
                    )}
                </div>

                {/* ACTION SUBMISSION HUB OVERVIEW DASHBOARD */}
                <div className="bg-white dark:bg-gray-800 p-6 rounded-xl border-t-4 border-rose-500 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4 mt-8">
                    <div className="text-sm text-gray-500">
                      Total de Items Modificados: <span className="font-bold text-gray-800 dark:text-gray-200">{draftItems.length}</span>
                    </div>

                    <div className="flex space-x-4">
                        <Link href={route('reconductions.index')} className="px-4 py-2 text-gray-600 font-bold hover:underline">
                            ← Volver
                        </Link>
                        
                        {can_edit && (
                            <>
                                <SecondaryButton onClick={saveDraftDetails} className="border-gray-300 border-2">
                                    💾 GUARDAR BORRADOR
                                </SecondaryButton>
                                <PrimaryButton onClick={submitReconduction} className="bg-indigo-600 hover:bg-indigo-700">
                                    📤 ENVIAR PARA VALIDACIÓN
                                </PrimaryButton>
                            </>
                        )}

                        {can_validate && (
                            <PrimaryButton onClick={approveReconduction} className="bg-green-600 hover:bg-green-700 py-3 shadow-lg hover:scale-105 transition-all">
                                ✅ APROBAR DICTAMEN 
                                <br/><span className="text-[10px] font-normal uppercase opacity-75 inline-block w-full text-center">(RIESGO LEGAL: ESTO REESCRIBIRÁ EL PBR)</span>
                            </PrimaryButton>
                        )}
                    </div>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}
