import { HttpClient } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { environment } from '../environments/environment';

interface HealthResponse {
  status: string;
  service: string;
  timestamp: string;
}

@Component({
  imports: [RouterOutlet],
  selector: 'app-root',
  styleUrl: './app.scss',
  templateUrl: './app.html',
})
export class App {
  protected readonly title = signal('frontend');
  protected readonly backendStatus = signal<'checking' | 'ok' | 'unreachable'>('checking');

  private readonly http = inject(HttpClient);

  constructor() {
    this.http.get<HealthResponse>(`${environment.apiBaseUrl}/api/health`).subscribe({
      next: () => this.backendStatus.set('ok'),
      error: () => this.backendStatus.set('unreachable'),
    });
  }
}
