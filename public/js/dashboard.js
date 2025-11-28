let calendarInstance;
let currentRequestId = null;
let pendingRequestsData = window.LMS.pendingRequests || [];

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileOverlay');
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

function loadUsers() {
    $.get(window.LMS.baseUrl + '/api/users', function(data) {
        let html = '';
        if(data.length === 0) {
            html = '<tr><td colspan="4" class="text-center py-8 text-zinc-500">No users found</td></tr>';
        } else {
            data.forEach(u => {
                let roleColor = 'bg-zinc-800 text-zinc-400 border-zinc-700';
                if(u.role === 'admin') roleColor = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                if(u.role === 'manager') roleColor = 'bg-amber-500/10 text-amber-400 border-amber-500/20';

                html += `
                <tr class="border-b border-zinc-800/50 last:border-0 hover:bg-zinc-900/50 transition-colors group">
                    <td class="py-3 pl-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-300">
                                ${u.full_name.substring(0,2).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-medium text-zinc-200">${u.full_name}</div>
                                <div class="text-[10px] text-zinc-500">@${u.username}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold border ${roleColor}">${u.role}</span>
                    </td>
                    <td class="py-3 text-zinc-400 font-mono text-xs">
                        ${u.vacation_balance} <span class="text-zinc-600">/</span> ${u.sick_balance}
                    </td>
                    <td class="py-3 text-right pr-2">
                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick='editUser(${JSON.stringify(u)})' class="p-1.5 hover:bg-zinc-800 rounded text-zinc-400 hover:text-white transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button onclick="deleteUser(${u.id})" class="p-1.5 hover:bg-rose-900/30 rounded text-zinc-400 hover:text-rose-500 transition" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
        }
        $('#usersTableBody').html(html);
    }, 'json');
}

function switchView(viewName) {
    if (window.innerWidth < 768) toggleSidebar();
    $('.view-section').removeClass('active');
    $('.sidebar-link').removeClass('active');
    $('#view-' + viewName).addClass('active');
    $('#nav-' + viewName).addClass('active');

    const titles = {
        'overview': 'Dashboard',
        'calendar': 'Calendar',
        'new-request': 'New Request',
        'approvals': 'Approvals',
        'users': 'Manage Users'
    };
    if (titles[viewName]) $('#pageTitle').text(titles[viewName]);
    if (viewName === 'calendar' && calendarInstance) setTimeout(() => calendarInstance.render(), 50);
    if (viewName === 'users') loadUsers(); 
}

function toggleModal(id) {
    $('#' + id).fadeToggle(200);
}

function openUserModal() {
    $('#userModalTitle').text('Add User');
    $('#userForm')[0].reset();
    $('#userIdInput').val('');
    $('#u_password').attr('required', 'required').attr('placeholder', 'Required for new users');
    $('#u_vacation').val(15);
    $('#u_sick').val(10);
    
    toggleModal('userModal');
}

function editUser(user) {
    $('#userModalTitle').text('Edit User');
    $('#userIdInput').val(user.id);
    $('#u_fullname').val(user.full_name);
    $('#u_username').val(user.username);
    $('#u_role').val(user.role);
    $('#u_vacation').val(user.vacation_balance);
    $('#u_sick').val(user.sick_balance);
    $('#u_password').removeAttr('required').attr('placeholder', 'Leave empty to keep current');
    toggleModal('userModal');
}

function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the user and all their history.",
        icon: 'warning',
        background: '#18181b',
        color: '#fff',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#3f3f46',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(window.LMS.baseUrl + '/api/user/delete', { id: id }, function(resp) {
                if(resp.status === 'success') {
                    Swal.fire({
                        title: 'Deleted!',
                        icon: 'success',
                        background: '#18181b',
                        color: '#fff',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    loadUsers();
                } else {
                    Swal.fire({ title: 'Error', text: resp.message, icon: 'error', background: '#18181b', color: '#fff'});
                }
            }, 'json');
        }
    })
}

function openRequestModal(requestId) {
    const data = pendingRequestsData.find(r => r.id == requestId);

    if (!data) {
        console.error("Request data not found for ID:", requestId);
        return;
    }

    currentRequestId = data.id;
    $('#reqName').text(data.full_name);
    $('#reqType').text(data.leave_type);
    $('#reqStart').text(data.start_date);
    $('#reqEnd').text(data.end_date);
    $('#reqReason').html(data.reason || 'No specific reason provided.');
    toggleModal('requestModal');
}

function openProfileModal() {
    $.get(window.LMS.baseUrl + '/api/profile', function(data) {
        if (data) {
            $('#profileName').text(data.full_name);
            $('#profileUsername').text('@' + data.username);
            $('#profileId').text('#' + String(data.id).padStart(4, '0'));
            $('#profileRoleBadge').text(data.role);

            let initials = data.full_name.match(/(\b\S)?/g).join("").match(/(^\S|\S$)?/g).join("").toUpperCase();
            $('#profileAvatar').css('background-color', '#27272a');
            $('#profileAvatar').text(initials);

            if (data.role === 'employee') {
                $('#p_vacation').text(data.vacation_balance);
                $('#p_sick').text(data.sick_balance);
                $('#profileBalances').removeClass('hidden');
            } else {
                $('#profileBalances').addClass('hidden');
            }

            toggleModal('profileModal');
        }
    }, 'json');
}

function processRequest(status) {
    if (!currentRequestId) return;
    $.post(window.LMS.baseUrl + '/api/leave/status', {
        request_id: currentRequestId,
        status: status
    }, function(resp) {
        if (resp.status === 'success') {
            toggleModal('requestModal');
            Swal.fire({
                title: status,
                icon: 'success',
                background: '#18181b',
                color: '#fff',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        }
    }, 'json');
}

function loadCharts() {
    if (!document.getElementById('typeChart') && !document.getElementById('lineChart')) return;

    $.get(window.LMS.baseUrl + '/api/charts', function(data) {
        if (document.getElementById('typeChart')) {
            const total = data.doughnut.reduce((a, b) => a + b, 0);
            $('#totalRequestsCenter').text(total);

            new Chart(document.getElementById('typeChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Vacation', 'Sick', 'Other'],
                    datasets: [{
                        data: data.doughnut,
                        backgroundColor: ['#a1a1aa', '#52525b', '#27272a'],
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    },
                }
            });

            const labels = ['Vacation', 'Sick', 'Other'];
            const colors = ['#a1a1aa', '#52525b', '#27272a'];
            let legendHtml = '';
            labels.forEach((label, index) => {
                let val = data.doughnut[index];
                legendHtml += `<div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full" style="background-color: ${colors[index]}"></span><span>${label} (${val})</span></div>`;
            });
            $('#customLegend').html(legendHtml);
        }
        if (document.getElementById('lineChart')) {
            const ctx = document.getElementById('lineChart').getContext('2d');
            let gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(255, 255, 255, 0.1)');
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Requests',
                        data: data.line,
                        borderColor: '#71717a',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { grid: { color: '#27272a' }, ticks: { color: '#52525b' } },
                        x: { grid: { display: false }, ticks: { color: '#52525b' } }
                    }
                }
            });
        }
    }, 'json');
}

function loadLogs() {
    $.get(window.LMS.baseUrl + '/api/logs', function(data) {
        let html = '';
        data.forEach(log => {
            html += `<div class="flex gap-3 pb-3 border-b border-zinc-900 last:border-0"><div class="text-zinc-500 text-xs pt-1"><i class="fa-solid fa-circle-dot text-[6px]"></i></div><div><p class="text-xs text-zinc-300"><span class="font-bold text-white">${log.username || 'Sys'}</span> ${log.action_text}</p><p class="text-[10px] text-zinc-600 font-mono mt-0.5">${log.created_at}</p></div></div>`;
        });
        $('#activityFeed').html(html);
    }, 'json');
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.LMS.role !== 'employee') {
        loadCharts();
    }

    if (window.LMS.role === 'admin') {
        loadLogs();
        setInterval(loadLogs, 10000);
    }

    var calendarEl = document.getElementById('calendar');
    calendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: window.innerWidth < 768 ? 'listWeek' : 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        events: window.LMS.baseUrl + '/api/calendar',
        height: 'auto',
        themeSystem: 'standard',
        eventContent: function(arg) {
            let title = arg.event.title;

            if (arg.view.type === 'listWeek') {
                let icon = '<i class="fa-solid fa-circle text-[6px]"></i>';
                let colorClass = 'bg-zinc-800 text-zinc-400 border-zinc-700';

                if (title.includes('Vacation')) {
                    icon = '<i class="fa-solid fa-plane text-xs"></i>';
                    colorClass = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
                } else if (title.includes('Sick')) {
                    icon = '<i class="fa-solid fa-heart-pulse text-xs"></i>';
                    colorClass = 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                } else if (title.includes('Holiday')) {
                    icon = '<i class="fa-solid fa-mug-hot text-xs"></i>';
                    colorClass = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                }

                return {
                    html: `
                        <div class="flex items-center gap-3 md:gap-4">
                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl flex items-center justify-center border ${colorClass} shrink-0">
                                ${icon}
                            </div>
                            <span class="text-xs md:text-sm font-medium text-zinc-200 truncate">${title}</span>
                        </div>
                    `
                };
            } else {
                let dotColor = 'bg-zinc-500';
                if (title.includes('Vacation')) dotColor = 'bg-zinc-300';
                else if (title.includes('Sick')) dotColor = 'bg-zinc-600';

                return {
                    html: `<div class="px-1.5 py-0.5 rounded text-[10px] bg-zinc-800 text-zinc-300 border border-zinc-700 truncate flex items-center gap-1.5"><div class="w-1 h-1 rounded-full ${dotColor}"></div>${title}</div>`
                }
            }
        }
    });
    calendarInstance.render();
});

$('#addEventForm').submit(function(e) {
    e.preventDefault();
    $.post(window.LMS.baseUrl + '/api/event/add', $(this).serialize(), function(resp) {
        if (resp.status === 'success') location.reload();
    }, 'json');
});

$('#userForm').submit(function(e) {
    e.preventDefault();
    
    const id = $('#userIdInput').val();
    const url = id ? '/api/user/update' : '/api/user/add';
    
    $.post(window.LMS.baseUrl + url, $(this).serialize(), function(resp) {
        if (resp.status === 'success') {
            toggleModal('userModal');
            $('#userForm')[0].reset();
            Swal.fire({
                icon: 'success',
                title: id ? 'User Updated' : 'User Created',
                background: '#18181b',
                color: '#fff',
                showConfirmButton: false,
                timer: 1500
            });

            loadUsers();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: resp.message,
                background: '#18181b',
                color: '#fff'
            });
        }
    }, 'json');
});

$('#leaveForm').submit(function(e) {
    e.preventDefault();
    $.post(window.LMS.baseUrl + '/api/leave/submit', $(this).serialize(), function(resp) {
        if (resp.status === 'success') Swal.fire({
            icon: 'success',
            title: 'Submitted',
            background: '#18181b',
            color: '#fff',
            showConfirmButton: false,
            timer: 1500
        }).then(() => location.reload());
        else Swal.fire({
            icon: 'error',
            title: 'Blocked',
            text: resp.message,
            background: '#18181b',
            color: '#fff'
        });
    }, 'json');
});

$('#logoutBtn').click(() => $.post(window.LMS.baseUrl + '/api/logout', {}, () => window.location.href = window.LMS.baseUrl + '/'));