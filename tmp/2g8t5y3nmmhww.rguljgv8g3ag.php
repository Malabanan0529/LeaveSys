<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Report - Preview</title>
    <script src="<?= ($BASE) ?>/public/js/library/TailWind-3.4.17"></script>
    <link href="<?= ($BASE) ?>/public/css/fonts/css2.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= ($BASE) ?>/public/css/library/all.min.css">
    <link rel="stylesheet" href="<?= ($BASE) ?>/public/css/report.css">
</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen p-8">

    <!-- Actions Bar -->
    <div class="max-w-5xl mx-auto mb-8 flex justify-between items-center no-print">
        <div>
            <h1 class="text-2xl font-semibold text-white">Report Preview</h1>
            <p class="text-zinc-500 text-sm mt-1">Review the data below before printing</p>
        </div>
        <div class="flex gap-4">
            <button onclick="window.close()" class="px-6 py-2.5 rounded-lg border border-zinc-800 text-zinc-400 hover:text-white hover:bg-zinc-900 transition-colors text-sm font-medium">
                Close Tab
            </button>
            <button onclick="window.print()" class="px-6 py-2.5 rounded-lg bg-white text-zinc-950 hover:bg-zinc-200 transition-colors text-sm font-bold shadow-lg shadow-white/5 flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Report Card -->
    <div class="max-w-5xl mx-auto glass-panel bg-zinc-900 border border-zinc-800 p-12 rounded-xl shadow-2xl relative">
        
        <!-- Report Header -->
        <div class="text-center border-b border-zinc-800 pb-8 mb-8">
            <h2 class="text-3xl font-bold text-white mb-2 uppercase tracking-widest text-zinc-950-print">Leave Summary Report</h2>
            <p class="text-zinc-500">Generated on <script>document.write(new Date().toLocaleDateString())</script></p>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-zinc-700">
                        <th class="py-4 text-xs uppercase tracking-wider text-zinc-500 font-semibold">Employee</th>
                        <th class="py-4 text-xs uppercase tracking-wider text-zinc-500 font-semibold">Leave Type</th>
                        <th class="py-4 text-xs uppercase tracking-wider text-zinc-500 font-semibold">Duration</th>
                        <th class="py-4 text-xs uppercase tracking-wider text-zinc-500 font-semibold text-center">Days</th>
                        <th class="py-4 text-xs uppercase tracking-wider text-zinc-500 font-semibold text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach (($reportData?:[]) as $row): ?>
                    <tr class="border-b border-zinc-800 hover:bg-zinc-800/50 transition-colors">
                        <td class="py-4 font-medium text-zinc-200"><?= ($row['full_name']) ?></td>
                        <td class="py-4 text-zinc-400"><?= ($row['leave_type']) ?></td>
                        <td class="py-4 text-zinc-400 font-mono text-xs">
                            <?= ($row['start_date']) ?> <span class="mx-1 text-zinc-600">to</span> <?= ($row['end_date'])."
" ?>
                        </td>
                        <td class="py-4 text-zinc-300 text-center font-bold"><?= ($row['days_count']) ?></td>
                        <td class="py-4 text-right">
                            <span class="badge px-2 py-1 rounded text-[10px] uppercase font-bold tracking-wide border
                                <?= ($row['status']=='Approved' ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' : 
                                   ($row['status']=='Rejected' ? 'bg-rose-500/10 text-rose-500 border-rose-500/20' : 
                                   'bg-amber-500/10 text-amber-500 border-amber-500/20')) ?>">
                                <?= ($row['status'])."
" ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-12 pt-8 border-t border-zinc-800 flex justify-between items-center text-xs text-zinc-600">
            <p>LMS - Leave Management System</p>
            <p>Confidential Document</p>
        </div>
    </div>
</body>
</html>