import math

def newton_raphson_meniru(x0: float, eps: float = 1e-5, max_iter: int = 50):
    def f(x):
        return math.exp(x) - 5*x*x
    def df(x):
        return math.exp(x) - 10*x

    rows = []
    # Bulatkan tebakan awal ke 6 desimal seperti di tabel
    x_old = round(x0, 6)
    rows.append((0, x_old, None))

    for i in range(1, max_iter+1):
        fx = f(x_old)
        dfx = df(x_old)
        if abs(dfx) < 1e-14:
            break
        x_new = x_old - fx/dfx
        # PEMBULATAN INTERNAL (yang menyebabkan perbedaan)
        x_new = round(x_new, 6)
        delta = abs(x_new - x_old)
        rows.append((i, x_new, delta))
        if delta < eps:
            break
        x_old = x_new
    return rows

def cetak_tabel(rows):
    print("------------------------------------------------------------")
    print(f"{'i':>3} {'x_r':>12} {'|x_{r+1}-x_r|':>14}")
    print("------------------------------------------------------------")
    for i, x, d in rows:
        if d is None:
            print(f"{i:3d} {x:12.6f} {'-':>14}")
        else:
            print(f"{i:3d} {x:12.6f} {d:14.6f}")
    print("------------------------------------------------------------")
    print(f"Hampiran akar  x = {rows[-1][1]:.6f}")

if __name__ == "__main__":
    data = newton_raphson_meniru(0.5, eps=1e-5)
    cetak_tabel(data)